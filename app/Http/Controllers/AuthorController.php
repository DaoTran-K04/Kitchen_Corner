<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthorController extends Controller
{
    /**
     * Display a paginated list of authors (aggregated from books table)
     * Tách các tên tác giả phân cách bằng dấu phẩy thành các entry riêng
     */
    public function index(Request $request)
    {
        $q = $request->get('q');
        $sort = $request->get('sort', 'popular');

        // Lấy tất cả author_name từ books (không bao gồm soft-deleted)
        $booksAuthors = DB::table('books')
            ->whereNotNull('author_name')
            ->where('author_name', '<>', '')
            ->where('is_approved', true)
            ->whereNull('deleted_at') // Loại bỏ soft-deleted
            ->select('author_name')
            ->get();

        // Tách các tên tác giả và đếm số sách
        $authorCounts = [];
        foreach ($booksAuthors as $book) {
            // Tách bằng dấu phẩy, dấu chấm phẩy, hoặc " và "
            $names = preg_split('/[,;]|\s+và\s+|\s+and\s+/iu', $book->author_name);
            foreach ($names as $name) {
                $name = trim($name);
                if (empty($name))
                    continue;

                if (!isset($authorCounts[$name])) {
                    $authorCounts[$name] = 0;
                }
                $authorCounts[$name]++;
            }
        }

        // Lọc theo từ khóa tìm kiếm
        if ($q) {
            $authorCounts = array_filter($authorCounts, function ($count, $name) use ($q) {
                return stripos($name, $q) !== false;
            }, ARRAY_FILTER_USE_BOTH);
        }

        // Lấy thông tin từ bảng authors
        $authorNames = array_keys($authorCounts);
        $authorsInfo = Author::whereIn('name', $authorNames)->get()->keyBy('name');

        // Tạo collection các tác giả
        $authorsData = collect($authorCounts)->map(function ($count, $name) use ($authorsInfo) {
            $info = $authorsInfo->get($name);
            return (object) [
                'name' => $name,
                'books_count' => $count,
                'photo' => $info->photo ?? null,
                'birth_year' => $info->birth_year ?? null,
                'death_year' => $info->death_year ?? null,
                'author_slug' => $info->slug ?? Str::slug($name),
                'bio' => $info->bio ?? null,
                'nationality' => $info->nationality ?? null,
            ];
        });

        // Sắp xếp
        if ($sort === 'name') {
            $authorsData = $authorsData->sortBy('name');
        } else {
            $authorsData = $authorsData->sortByDesc('books_count');
        }

        // Phân trang thủ công
        $page = $request->get('page', 1);
        $perPage = 24;
        $total = $authorsData->count();
        $authors = new \Illuminate\Pagination\LengthAwarePaginator(
            $authorsData->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('authors.index', compact('authors', 'q', 'sort'));
    }

    /**
     * Hiển thị chi tiết tác giả (public)
     * Hỗ trợ tìm sách có tên tác giả nằm trong chuỗi phân cách bằng dấu phẩy
     */
    public function show($slug)
    {
        // Tìm trong bảng authors trước
        $author = Author::where('slug', $slug)->first();

        $authorName = $author ? $author->name : str_replace('-', ' ', $slug);

        // Tìm sách có chứa tên tác giả (hỗ trợ nhiều dạng phân cách)
        // Đồng thời bao gồm các sách đã được gắn qua bảng pivot `author_book`.
        $books = Book::where('is_approved', true)
            ->where(function ($query) use ($authorName, $author) {
                // Tìm bằng LIKE với % ở cả 2 đầu để bắt mọi trường hợp
                $query->where('author_name', $authorName) // Exact match
                    ->orWhere('author_name', 'like', '%' . $authorName . '%'); // Substring match
    
                // Nếu tác giả tồn tại trong bảng authors, thêm các sách được liên kết qua pivot
                if ($author && isset($author->id)) {
                    $query->orWhereHas('authors', function ($q) use ($author) {
                        $q->where('authors.id', $author->id);
                    });
                }
            })
            ->paginate(12);

        // Nếu không tìm thấy author trong bảng authors, tạo object giả
        if (!$author) {
            $author = (object) [
                'name' => $authorName,
                'slug' => $slug,
                'photo' => null,
                'bio' => null,
                'birth_year' => null,
                'death_year' => null,
                'nationality' => null,
            ];
        }

        return view('authors.show', compact('author', 'books'));
    }

    // ========== ADMIN ONLY ==========

    /**
     * Danh sách tác giả cho Admin quản lý
     * Chỉ hiện tác giả đã đăng ký trong bảng authors
     */
    public function adminIndex(Request $request)
    {
        $query = Author::withCount([
            'books' => function ($q) {
                $q->where('is_approved', true);
            }
        ])->orderBy('name');

        // Lọc theo quốc tịch
        $nationality = $request->get('nationality');
        if ($nationality) {
            $query->where('nationality', $nationality);
        }

        // Lọc theo từ khóa tìm kiếm
        $q = $request->get('q');
        if ($q) {
            $query->where('name', 'like', '%' . $q . '%');
        }

        // Phân trang
        $authors = $query->paginate(15)->withQueryString();

        // Lấy danh sách quốc tịch để hiển thị dropdown
        $nationalities = Author::whereNotNull('nationality')
            ->where('nationality', '<>', '')
            ->distinct()
            ->orderBy('nationality')
            ->pluck('nationality');

        // Nếu là AJAX request, chỉ trả về table partial
        if ($request->ajax()) {
            return view('admin.authors.table', compact('authors'));
        }

        return view('admin.authors.index', compact('authors', 'nationalities', 'q'));
    }

    /**
     * Proxy ảnh để tránh lỗi CORS
     */
    public function proxyImage(Request $request)
    {
        $url = $request->input('url');
        if (!$url)
            return response()->json(['error' => 'URL Required'], 400);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()->get($url);

            if ($response->failed())
                throw new \Exception('Failed to fetch image');

            $contentType = $response->header('Content-Type') ?: 'image/jpeg';

            return response($response->body())
                ->header('Content-Type', $contentType)
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not load image'], 400);
        }
    }

    /**
     * Form thêm tác giả mới (Admin only)
     */
    public function create()
    {
        $nationalities = Author::whereNotNull('nationality')
            ->where('nationality', '<>', '')
            ->distinct()
            ->orderBy('nationality')
            ->pluck('nationality');

        return view('admin.authors.create', compact('nationalities'));
    }

    /**
     * Lưu tác giả mới (Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('authors', 'name')->whereNull('deleted_at')],
            'photo' => 'nullable|string|max:500',
            'photo_file' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:2048',
            'cropped_photo' => 'nullable|string',
            'bio' => 'nullable|string',
            'birth_year' => 'nullable|integer|min:0|max:' . date('Y'),
            'death_year' => 'nullable|integer|min:0|max:' . date('Y') . '|gte:birth_year',
            'nationality' => 'nullable|string|max:100',
        ], [
            'death_year.gte' => 'Năm mất phải lớn hơn hoặc bằng năm sinh.',
        ]);

        // Handle cropped base64 image (priority)
        if (!empty($request->cropped_photo)) {
            $base64 = $request->cropped_photo;
            // Extract base64 data
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                $ext = $matches[1];
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $image = base64_decode($base64);
                $filename = 'authors/' . uniqid() . '.' . $ext;
                \Storage::disk('public')->put($filename, $image);
                $validated['photo'] = $filename;
            }
        }
        // Handle file upload
        elseif ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('authors', 'public');
            $validated['photo'] = $path;
        }

        unset($validated['photo_file'], $validated['cropped_photo']);

        $validated['slug'] = Str::slug($validated['name']);

        Author::create($validated);

        return redirect()->route('admin.authors.index', ['tab' => 'registered'])
            ->with('success', 'Đã thêm tác giả thành công!');
    }

    /**
     * Form chỉnh sửa tác giả (Admin only)
     */
    public function edit($id)
    {
        $author = Author::findOrFail($id);
        $nationalities = Author::whereNotNull('nationality')
            ->where('nationality', '<>', '')
            ->distinct()
            ->orderBy('nationality')
            ->pluck('nationality');

        return view('admin.authors.edit', compact('author', 'nationalities'));
    }

    /**
     * Cập nhật tác giả (Admin only)
     */
    public function update(Request $request, $id)
    {
        $author = Author::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('authors', 'name')->ignore($id)->whereNull('deleted_at')],
            'photo' => 'nullable|string|max:500',
            'photo_file' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:2048',
            'cropped_photo' => 'nullable|string',
            'bio' => 'nullable|string',
            'birth_year' => 'nullable|integer|min:0|max:' . date('Y'),
            'death_year' => 'nullable|integer|min:0|max:' . date('Y') . '|gte:birth_year',
            'nationality' => 'nullable|string|max:100',
        ], [
            'death_year.gte' => 'Năm mất phải lớn hơn hoặc bằng năm sinh.',
        ]);

        // Delete old file helper
        $deleteOldPhoto = function () use ($author) {
            if ($author->photo && !str_starts_with($author->photo, 'http')) {
                \Storage::disk('public')->delete($author->photo);
            }
        };

        // Handle cropped base64 image (priority)
        if (!empty($request->cropped_photo)) {
            $base64 = $request->cropped_photo;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                $deleteOldPhoto();
                $ext = $matches[1];
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $image = base64_decode($base64);
                $filename = 'authors/' . uniqid() . '.' . $ext;
                \Storage::disk('public')->put($filename, $image);
                $validated['photo'] = $filename;
            }
        }
        // Handle file upload
        elseif ($request->hasFile('photo_file')) {
            $deleteOldPhoto();
            $path = $request->file('photo_file')->store('authors', 'public');
            $validated['photo'] = $path;
        }

        unset($validated['photo_file'], $validated['cropped_photo']);

        $validated['slug'] = Str::slug($validated['name']);

        $author->update($validated);

        return redirect()->route('admin.authors.index')
            ->with('success', 'Đã cập nhật tác giả thành công!');
    }

    /**
     * Xóa tác giả (Admin only)
     */
    public function destroy($id)
    {
        $author = Author::findOrFail($id);
        $authorData = $author->toArray();

        $author->delete();

        // Ghi log để có thể khôi phục
        \App\Models\AdminActivityLog::log(
            'delete',
            "Xóa Tác giả: {$authorData['name']}",
            Author::class,
            $authorData['id'],
            $authorData,
            null
        );

        return redirect()->route('admin.authors.index')
            ->with('success', 'Đã xóa tác giả thành công!');
    }
}