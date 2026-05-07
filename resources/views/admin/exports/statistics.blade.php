<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        .header {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            color: #1e293b;
            background-color: #f8fafc;
        }
        .subheader {
            font-size: 10pt;
            text-align: center;
            color: #64748b;
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #3b82f6;
            color: #ffffff;
            font-weight: bold;
            padding: 8px;
            font-size: 12pt;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: left;
        }
        td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            vertical-align: top;
        }
        .stt { text-align: center; width: 40px; }
        .number { text-align: right; }
        .status-published { color: #10b981; font-weight: bold; }
        .status-pending { color: #f59e0b; font-weight: bold; }
        .footer-label { font-weight: bold; background-color: #f8fafc; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="8" class="header">
                BÁO CÁO THỐNG KÊ HỆ THỐNG GÓC BẾP - THÁNG {{ $selectedMonth }}/{{ $selectedYear }}
            </td>
        </tr>
        <tr>
            <td colspan="8" class="subheader">
                Ngày xuất báo cáo: {{ date('d/m/Y H:i') }} | Người thực hiện: Admin Hệ Thống
            </td>
        </tr>
        <tr><td colspan="8"></td></tr>

        {{-- Section 1: Công thức --}}
        <tr>
            <td colspan="8" class="section-title">
                I. DANH SÁCH CÔNG THỨC MỚI ({{ $recipes->count() }} mục)
            </td>
        </tr>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên công thức</th>
                <th>Người đăng</th>
                <th>Email liên hệ</th>
                <th>Lượt xem</th>
                <th>Bình luận</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recipes as $index => $recipe)
            <tr>
                <td class="stt">{{ $index + 1 }}</td>
                <td>{{ $recipe->title }}</td>
                <td>{{ $recipe->user->name ?? 'N/A' }}</td>
                <td>{{ $recipe->user->email ?? 'N/A' }}</td>
                <td class="number">{{ number_format($recipe->view_count) }}</td>
                <td class="number">{{ number_format($recipe->comments_count) }}</td>
                <td class="{{ $recipe->status === 'published' ? 'status-published' : 'status-pending' }}">
                    {{ $recipe->status === 'published' ? 'Đã duyệt' : 'Chờ duyệt' }}
                </td>
                <td>{{ $recipe->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>

        <tr><td colspan="8"></td></tr>

        {{-- Section 2: Thành viên --}}
        <tr>
            <td colspan="8" class="section-title">
                II. THÀNH VIÊN MỚI TRONG THÁNG ({{ $users->count() }} người)
            </td>
        </tr>
        <thead>
            <tr>
                <th>STT</th>
                <th colspan="2">Họ tên</th>
                <th colspan="3">Email</th>
                <th>Số công thức</th>
                <th>Ngày gia nhập</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td class="stt">{{ $index + 1 }}</td>
                <td colspan="2">{{ $user->name }}</td>
                <td colspan="3">{{ $user->email }}</td>
                <td class="number">{{ $user->recipes_count ?? 0 }}</td>
                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>

        <tr><td colspan="8"></td></tr>

        {{-- Section 3: Top Công thức --}}
        @if($topRecipes->count() > 0)
        <tr>
            <td colspan="8" class="section-title">
                III. TOP 5 CÔNG THỨC THU HÚT NHẤT
            </td>
        </tr>
        <thead>
            <tr>
                <th>Hạng</th>
                <th colspan="4">Tên công thức</th>
                <th colspan="2">Tác giả</th>
                <th>Lượt xem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topRecipes as $index => $recipe)
            <tr>
                <td class="stt">{{ $index + 1 }}</td>
                <td colspan="4">{{ $recipe->title }}</td>
                <td colspan="2">{{ $recipe->user->name ?? 'Ẩn danh' }}</td>
                <td class="number">{{ number_format($recipe->view_count) }}</td>
            </tr>
            @endforeach
        </tbody>
        @endif

        <tr><td colspan="8"></td></tr>

        {{-- Section 4: Tổng hợp --}}
        <tr>
            <td colspan="8" class="section-title">
                IV. CHỈ SỐ TỔNG HỢP TOÀN HỆ THỐNG
            </td>
        </tr>
        <tr>
            <td colspan="4" class="footer-label">Tổng lượng công thức mới:</td>
            <td colspan="4" class="number">{{ $recipes->count() }}</td>
        </tr>
        <tr>
            <td colspan="4" class="footer-label">Tỷ lệ duyệt bài:</td>
            <td colspan="4" class="number">
                {{ $recipes->count() > 0 ? round(($recipes->where('status', 'published')->count() / $recipes->count()) * 100, 1) : 0 }}% 
                ({{ $recipes->where('status', 'published')->count() }} bài)
            </td>
        </tr>
        <tr>
            <td colspan="4" class="footer-label">Tổng lượt xem tích lũy:</td>
            <td colspan="4" class="number">{{ number_format($totalViews) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="footer-label">Tổng tương tác (Like & Comment):</td>
            <td colspan="4" class="number">{{ number_format($totalLikes + $totalComments) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="footer-label">Tăng trưởng thành viên:</td>
            <td colspan="4" class="number">{{ $users->count() }} người dùng mới</td>
        </tr>
    </table>
</body>
</html>
