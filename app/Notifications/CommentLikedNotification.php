<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // Nên dùng Queue để web chạy mượt
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class CommentLikedNotification extends Notification //implements ShouldQueue
{
    use Queueable;

    protected $user; // Người vừa bấm like
    protected $comment; // Comment được like

    public function __construct($user, $comment)
    {
        $this->user = $user;
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database']; // Lưu vào database
    }

    public function toDatabase($notifiable)
    {
    // 1. Lấy Bài Review (Post) từ Comment
    $post = $this->comment->post;
    
    // 2. Lấy Cuốn Sách (Book) từ Bài Review
    // (Giả sử trong Model Post bạn có hàm book() { return $this->belongsTo(Book::class); })
    $book = $post ? $post->book : null;

    // 3. Xử lý Slug an toàn (tránh lỗi null)
    $slug = '404'; 
    if ($book) {
        $slug = $book->slug;
    }

    return [
        'user_id' => $this->user->id,
        'user_name' => $this->user->name,
        'user_avatar' => $this->user->avatar,
        'message' => 'đã thích bình luận của bạn',
        'post_title' => $this->comment->content,
        
        // 4. Tạo Link: Đi tới trang chi tiết Sách -> Neo xuống đúng cái comment đó
        'link' => route('detail', ['slug' => $slug]) . '#comment-' . $this->comment->id
    ];
    }
}