<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Post;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Category;
class BookController extends Controller
{
    // --- HÀM HỖ TRỢ: Lấy sách kèm tính toán điểm trung bình ---
    private function getBookQuery()
    {
        return Book::where('is_approved', true)
            ->withAvg([
                'posts' => function ($q) {
                    $q->where('status', 'published');
                }
            ], 'rating');
    }

    // 1. TRANG CHỦ 
    public function index(Request $request)
    {
        $books = $this->getBookQuery()
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();


        $filter = $request->get('filter', 'latest');
        $query = Post::with(['user', 'book'])
            ->where('status', 'published')
            ->withCount(['likes', 'comments']);

        if ($filter == 'viewed') {
            $query->orderBy('view_count', 'desc');
        } elseif ($filter == 'liked') {
            $query->orderBy('likes_count', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $latestReviews = $query->take(6)->get();

        return view('home', [
            'books' => $books,
            'latestReviews' => $latestReviews,
            'currentFilter' => $filter
        ]);
    }

    public function home(Request $request)
    {
        return $this->index($request);
    }

    // 2. TRANG CHI TIẾT SÁCH
    public function show($id)
    {
        $query = $this->getBookQuery();

        if (is_numeric($id)) {
            $query->where('id', $id);
        } else {
            $query->where('slug', $id);
        }

        $book = $query->firstOrFail();
        $book->avg_rating = round($book->posts_avg_rating ?? 0, 1);

        $sessionKey = 'book_viewed_' . $book->id;
        if (!Session::has($sessionKey)) {
            $book->increment('view_count');
            Session::put($sessionKey, true);
        }

        $book->load([
            'categories',
            'author', // eager-load author từ bảng `authors` (nếu có)
            'posts' => function ($q) {
                $q->where('status', 'published')->latest();
            },
            'posts.user.activeBadges',
            'posts.likes', // Load likes của bài review
            'posts.comments' => function ($q) {
                $q->whereNull('parent_id')->latest(); // Chỉ load comment cha
            },
            'posts.comments.user.activeBadges',
            'posts.comments.likes',
            'posts.comments.replies' => function ($q) {
                $q->latest(); // Load replies của comment
            },
            'posts.comments.replies.user',
            'posts.comments.replies.likes'
        ]);

        // Lấy sách liên quan (cùng thể loại, loại trừ sách hiện tại)
        $categoryIds = $book->categories->pluck('id')->toArray();
        $relatedBooks = Book::where('is_approved', true)
            ->where('id', '!=', $book->id)
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->withAvg([
                'posts' => function ($q) {
                    $q->where('status', 'published');
                }
            ], 'rating')
            ->take(5)
            ->get();

        return view('book-detail', compact('book', 'relatedBooks'));
    }

    // 3. TÌM KIẾM (Đã thêm Validation số âm & Cảnh báo số đặc biệt)
    public function search(Request $request)
    {
        // 1. Lấy tham số
        $keyword = $request->input('keyword');
        $filterType = $request->input('filter_type', 'title');

        // --- VALIDATION & WARNING LOGIC ---

        // Kiểm tra nếu đang lọc theo tiêu chí số (view, rating, review) và có nhập liệu
        if ($keyword !== null && $keyword !== '' && in_array($filterType, ['view_count', 'avg_rating', 'total_reviews']) && is_numeric($keyword)) {

            // 1. Chặn số âm
            if ($keyword < 0) {
                // Trả về trang tìm kiếm (reset kết quả) kèm thông báo lỗi
                return redirect()->route('books.search', ['filter_type' => $filterType])
                    ->with('error', 'Giá trị tìm kiếm không được là số âm!');
            }

            // 2. Cảnh báo số đặc biệt (Ví dụ: 13, 666...)
            $specialNumbers = [13, 666, 0];
            if (in_array(intval($keyword), $specialNumbers)) {
                session()->flash('warning', "Bạn đang tìm kiếm con số đặc biệt ($keyword). Kết quả có thể rất ít hoặc không có!");
            }
        }
        // ----------------------------------

        // 2. Khởi tạo Query với withAvg để tính rating từ posts đã published
        $query = Book::where('is_approved', true)
            ->withAvg([
                'posts' => function ($q) {
                    $q->where('status', 'published');
                }
            ], 'rating')
            ->withCount([
                'posts' => function ($q) {
                    $q->where('status', 'published');
                }
            ]);

        // 3. Xử lý Lọc
        if ($keyword !== null && $keyword !== '') {
            switch ($filterType) {
                case 'view_count':
                    $query->where('view_count', '>=', intval($keyword))
                        ->orderBy('view_count', 'desc');
                    break;

                case 'avg_rating':
                    $query->having('posts_avg_rating', '>=', floatval($keyword))
                        ->orderBy('posts_avg_rating', 'desc');
                    break;

                case 'total_reviews':
                    $query->having('posts_count', '>=', intval($keyword))
                        ->orderBy('posts_count', 'desc');
                    break;

                case 'title':
                default:
                    $query->where(function ($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%")
                            ->orWhere('author_name', 'like', "%{$keyword}%");
                    })->orderBy('view_count', 'desc');
                    break;
            }
        } else {
            $query->orderBy('view_count', 'desc');
        }

        // 4. Phân trang
        $books = $query->paginate(10)->withQueryString();

        // 5. Gán avg_rating và total_reviews từ posts đã tính
        $books->getCollection()->transform(function ($book) {
            $book->avg_rating = round($book->posts_avg_rating ?? 0, 1);
            $book->total_reviews = $book->posts_count ?? 0;
            return $book;
        });

        return view('search-book', [
            'books' => $books
        ]);
    }

    // 4. SÁCH MỚI CẬP NHẬT
    public function list(Request $request)
    {
        // ... (Giữ nguyên logic query filter cũ của bạn) ...
        $query = $this->getBookQuery();

        if ($request->has('categories') && is_array($request->categories)) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->whereIn('name', $request->categories);
            });
        }

        if ($request->has('rating')) {
            $rating = (int) $request->rating;
            $query->having('posts_avg_rating', '>=', $rating);
        }

        // --- MỚI: Xử lý tìm kiếm theo từ khóa (Keyword) ---
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author_name', 'like', "%{$keyword}%");
            });
        }

        // ... (Giữ nguyên logic sort) ...
        // 4. Sắp xếp (Sorting)
        $sort = $request->get('sort', 'newest'); // Mặc định là mới nhất

        switch ($sort) {
            case 'view_desc':
                $query->orderBy('view_count', 'desc'); // Xem nhiều nhất
                break;

            case 'rating_desc':
                // Sắp xếp theo cột điểm trung bình (được tạo ra bởi withAvg)
                $query->orderBy('posts_avg_rating', 'desc');
                break;

            case 'title_asc':  // <--- THÊM MỚI: Sắp xếp tên A-Z (Nếu muốn)
                $query->orderBy('title', 'asc');
                break;

            case 'newest':
            default:
                $query->orderBy('created_at', 'desc'); // Mặc định: Mới nhất
                break;
        }

        $books = $query->paginate(12)->withQueryString();

        $books->getCollection()->transform(function ($book) {
            $book->avg_rating = round($book->posts_avg_rating ?? 0, 1);
            return $book;
        });

        // 2. LẤY DANH SÁCH THỂ LOẠI TỪ DB (Có thể thêm ->orderBy('name') cho đẹp)
        $categories = Category::all();

        // 3. TRUYỀN BIẾN $categories SANG VIEW
        return view('list', [
            'books' => $books,
            'categories' => $categories,
            'pageTitle' => 'Tất Cả Sách'
        ]);
    }

    // 5. TRANG HIỂN THỊ ĐÁNH GIÁ SÁCH
    public function showReviews($slug)
    {
        $book = $this->getBookQuery()
            ->where('slug', $slug)
            ->firstOrFail();

        $book->avg_rating = round($book->posts_avg_rating ?? 0, 1);

        // Lấy danh sách reviews (posts) có phân trang
        $reviews = Post::where('book_id', $book->id)
            ->where('status', 'published')
            ->with(['user.activeBadges', 'comments.user.activeBadges', 'likes'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(10);

        // Tăng view_count cho các bài review được hiển thị (chỉ tính 1 lần/session)
        foreach ($reviews as $review) {
            $sessionKey = 'review_viewed_' . $review->id;
            if (!Session::has($sessionKey)) {
                $review->increment('view_count');
                Session::put($sessionKey, true);
            }
        }

        return view('review-detail', [
            'book' => $book,
            'reviews' => $reviews,
            'pageTitle' => 'Đánh giá sách: ' . $book->title
        ]);
    }
}