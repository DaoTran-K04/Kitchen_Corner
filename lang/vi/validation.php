<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Trường :attribute phải được chấp nhận.',
    'accepted_if' => 'Trường :attribute phải được chấp nhận khi :other là :value.',
    'active_url' => 'Trường :attribute không phải là một URL hợp lệ.',
    'after' => 'Trường :attribute phải là ngày sau ngày :date.',
    'after_or_equal' => 'Trường :attribute phải là ngày sau hoặc bằng ngày :date.',
    'alpha' => 'Trường :attribute chỉ được chứa các chữ cái.',
    'alpha_dash' => 'Trường :attribute chỉ được chứa chữ cái, số, dấu gạch ngang và dấu gạch dưới.',
    'alpha_num' => 'Trường :attribute chỉ được chứa chữ cái và số.',
    'array' => 'Trường :attribute phải là một mảng.',
    'ascii' => 'Trường :attribute chỉ được chứa ký tự chữ số và ký hiệu một byte.',
    'before' => 'Trường :attribute phải là ngày trước ngày :date.',
    'before_or_equal' => 'Trường :attribute phải là ngày trước hoặc bằng ngày :date.',
    'between' => [
        'array' => 'Trường :attribute phải có từ :min đến :max phần tử.',
        'file' => 'Dung lượng tệp :attribute phải từ :min đến :max kilobytes.',
        'numeric' => 'Trường :attribute phải nằm trong khoảng :min đến :max.',
        'string' => 'Trường :attribute phải có độ dài từ :min đến :max ký tự.',
    ],
    'boolean' => 'Trường :attribute phải là true hoặc false.',
    'can' => 'Trường :attribute chứa giá trị không được phép.',
    'confirmed' => 'Xác nhận :attribute không khớp.',
    'current_password' => 'Mật khẩu không đúng.',
    'date' => 'Trường :attribute không phải là ngày hợp lệ.',
    'date_equals' => 'Trường :attribute phải là ngày :date.',
    'date_format' => 'Trường :attribute không khớp với định dạng :format.',
    'decimal' => 'Trường :attribute phải có :decimal chữ số thập phân.',
    'declined' => 'Trường :attribute phải bị từ chối.',
    'declined_if' => 'Trường :attribute phải bị từ chối khi :other là :value.',
    'different' => 'Trường :attribute và :other phải khác nhau.',
    'digits' => 'Trường :attribute phải có :digits chữ số.',
    'digits_between' => 'Trường :attribute phải có từ :min đến :max chữ số.',
    'dimensions' => 'Trường :attribute có kích thước hình ảnh không hợp lệ.',
    'distinct' => 'Trường :attribute có giá trị trùng lặp.',
    'doesnt_end_with' => 'Trường :attribute không được kết thúc bằng: :values.',
    'doesnt_start_with' => 'Trường :attribute không được bắt đầu bằng: :values.',
    'email' => 'Trường :attribute phải là địa chỉ email hợp lệ.',
    'ends_with' => 'Trường :attribute phải kết thúc bằng một trong các giá trị: :values.',
    'enum' => 'Giá trị đã chọn cho :attribute không hợp lệ.',
    'exists' => 'Giá trị đã chọn cho :attribute không tồn tại.',
    'extensions' => 'Trường :attribute phải có định dạng: :values.',
    'file' => 'Trường :attribute phải là một tệp.',
    'filled' => 'Trường :attribute phải có giá trị.',
    'gt' => [
        'array' => 'Trường :attribute phải có nhiều hơn :value phần tử.',
        'file' => 'Dung lượng tệp :attribute phải lớn hơn :value kilobytes.',
        'numeric' => 'Trường :attribute phải lớn hơn :value.',
        'string' => 'Trường :attribute phải có nhiều hơn :value ký tự.',
    ],
    'gte' => [
        'array' => 'Trường :attribute phải có ít nhất :value phần tử.',
        'file' => 'Dung lượng tệp :attribute phải lớn hơn hoặc bằng :value kilobytes.',
        'numeric' => 'Trường :attribute phải lớn hơn hoặc bằng :value.',
        'string' => 'Trường :attribute phải có ít nhất :value ký tự.',
    ],
    'hex_color' => 'Trường :attribute phải là mã màu thập lục phân hợp lệ.',
    'image' => 'Trường :attribute phải là hình ảnh.',
    'in' => 'Giá trị đã chọn cho :attribute không hợp lệ.',
    'in_array' => 'Trường :attribute không tồn tại trong :other.',
    'integer' => 'Trường :attribute phải là số nguyên.',
    'ip' => 'Trường :attribute phải là địa chỉ IP hợp lệ.',
    'ipv4' => 'Trường :attribute phải là địa chỉ IPv4 hợp lệ.',
    'ipv6' => 'Trường :attribute phải là địa chỉ IPv6 hợp lệ.',
    'json' => 'Trường :attribute phải là chuỗi JSON hợp lệ.',
    'lowercase' => 'Trường :attribute phải là chữ thường.',
    'lt' => [
        'array' => 'Trường :attribute phải có ít hơn :value phần tử.',
        'file' => 'Dung lượng tệp :attribute phải nhỏ hơn :value kilobytes.',
        'numeric' => 'Trường :attribute phải nhỏ hơn :value.',
        'string' => 'Trường :attribute phải có ít hơn :value ký tự.',
    ],
    'lte' => [
        'array' => 'Trường :attribute không được có nhiều hơn :value phần tử.',
        'file' => 'Dung lượng tệp :attribute phải nhỏ hơn hoặc bằng :value kilobytes.',
        'numeric' => 'Trường :attribute phải nhỏ hơn hoặc bằng :value.',
        'string' => 'Trường :attribute không được có nhiều hơn :value ký tự.',
    ],
    'mac_address' => 'Trường :attribute phải là địa chỉ MAC hợp lệ.',
    'max' => [
        'array' => 'Trường :attribute không được có nhiều hơn :max phần tử.',
        'file' => 'Dung lượng tệp :attribute không được vượt quá :max kilobytes.',
        'numeric' => 'Trường :attribute không được lớn hơn :max.',
        'string' => 'Trường :attribute không được có nhiều hơn :max ký tự.',
    ],
    'max_digits' => 'Trường :attribute không được có nhiều hơn :max chữ số.',
    'mimes' => 'Trường :attribute phải là tệp có định dạng: :values.',
    'mimetypes' => 'Trường :attribute phải là tệp có định dạng: :values.',
    'min' => [
        'array' => 'Trường :attribute phải có ít nhất :min phần tử.',
        'file' => 'Dung lượng tệp :attribute phải tối thiểu :min kilobytes.',
        'numeric' => 'Trường :attribute phải tối thiểu :min.',
        'string' => 'Trường :attribute phải có ít nhất :min ký tự.',
    ],
    'min_digits' => 'Trường :attribute phải có ít nhất :min chữ số.',
    'missing' => 'Trường :attribute không được có mặt.',
    'missing_if' => 'Trường :attribute không được có mặt khi :other là :value.',
    'missing_unless' => 'Trường :attribute không được có mặt trừ khi :other là :value.',
    'missing_with' => 'Trường :attribute không được có mặt khi :values có mặt.',
    'missing_with_all' => 'Trường :attribute không được có mặt khi :values có mặt.',
    'multiple_of' => 'Trường :attribute phải là bội số của :value.',
    'not_in' => 'Giá trị đã chọn cho :attribute không hợp lệ.',
    'not_regex' => 'Định dạng của :attribute không hợp lệ.',
    'numeric' => 'Trường :attribute phải là số.',
    'password' => [
        'letters' => 'Trường :attribute phải chứa ít nhất một chữ cái.',
        'mixed' => 'Trường :attribute phải chứa ít nhất một chữ hoa và một chữ thường.',
        'numbers' => 'Trường :attribute phải chứa ít nhất một chữ số.',
        'symbols' => 'Trường :attribute phải chứa ít nhất một ký tự đặc biệt.',
        'uncompromised' => ':attribute đã xuất hiện trong một vụ rò rỉ dữ liệu. Vui lòng chọn :attribute khác.',
    ],
    'present' => 'Trường :attribute phải có mặt.',
    'present_if' => 'Trường :attribute phải có mặt khi :other là :value.',
    'present_unless' => 'Trường :attribute phải có mặt trừ khi :other là :value.',
    'present_with' => 'Trường :attribute phải có mặt khi :values có mặt.',
    'present_with_all' => 'Trường :attribute phải có mặt khi :values có mặt.',
    'prohibited' => 'Trường :attribute bị cấm.',
    'prohibited_if' => 'Trường :attribute bị cấm khi :other là :value.',
    'prohibited_unless' => 'Trường :attribute bị cấm trừ khi :other nằm trong :values.',
    'prohibits' => 'Trường :attribute cấm :other có mặt.',
    'regex' => 'Định dạng của :attribute không hợp lệ.',
    'required' => 'Trường :attribute là bắt buộc.',
    'required_array_keys' => 'Trường :attribute phải chứa các khóa: :values.',
    'required_if' => 'Trường :attribute là bắt buộc khi :other là :value.',
    'required_if_accepted' => 'Trường :attribute là bắt buộc khi :other được chấp nhận.',
    'required_unless' => 'Trường :attribute là bắt buộc trừ khi :other nằm trong :values.',
    'required_with' => 'Trường :attribute là bắt buộc khi :values có mặt.',
    'required_with_all' => 'Trường :attribute là bắt buộc khi :values có mặt.',
    'required_without' => 'Trường :attribute là bắt buộc khi :values không có mặt.',
    'required_without_all' => 'Trường :attribute là bắt buộc khi không có :values nào có mặt.',
    'same' => 'Trường :attribute và :other phải khớp nhau.',
    'size' => [
        'array' => 'Trường :attribute phải chứa :size phần tử.',
        'file' => 'Dung lượng tệp :attribute phải bằng :size kilobytes.',
        'numeric' => 'Trường :attribute phải bằng :size.',
        'string' => 'Trường :attribute phải có :size ký tự.',
    ],
    'starts_with' => 'Trường :attribute phải bắt đầu bằng một trong các giá trị: :values.',
    'string' => 'Trường :attribute phải là chuỗi ký tự.',
    'timezone' => 'Trường :attribute phải là múi giờ hợp lệ.',
    'unique' => ':attribute đã tồn tại.',
    'uploaded' => ':attribute tải lên thất bại.',
    'uppercase' => 'Trường :attribute phải là chữ hoa.',
    'url' => 'Trường :attribute phải là URL hợp lệ.',
    'ulid' => 'Trường :attribute phải là ULID hợp lệ.',
    'uuid' => 'Trường :attribute phải là UUID hợp lệ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'title' => [
            'required' => 'Vui lòng nhập tiêu đề.',
            'max' => 'Tiêu đề không được vượt quá :max ký tự.',
        ],
        'content' => [
            'required' => 'Vui lòng nhập nội dung.',
        ],
        'author' => [
            'required' => 'Vui lòng nhập tên tác giả.',
        ],
        'author_name' => [
            'required' => 'Vui lòng nhập tên tác giả.',
        ],
        'name' => [
            'required' => 'Vui lòng nhập tên.',
            'max' => 'Tên không được vượt quá :max ký tự.',
        ],
        'email' => [
            'required' => 'Vui lòng nhập email.',
            'email' => 'Email không đúng định dạng.',
        ],
        'password' => [
            'required' => 'Vui lòng nhập mật khẩu.',
            'min' => 'Mật khẩu phải có ít nhất :min ký tự.',
        ],
        'thumbnail' => [
            'image' => 'Tệp tải lên phải là hình ảnh.',
            'mimes' => 'Hình ảnh phải có định dạng: :values.',
            'max' => 'Kích thước hình ảnh không được vượt quá :max KB.',
        ],
        'thumbnail_url' => [
            'url' => 'URL hình ảnh không hợp lệ.',
        ],
        'cover_image' => [
            'image' => 'Ảnh bìa phải là hình ảnh.',
            'mimes' => 'Ảnh bìa phải có định dạng: :values.',
            'max' => 'Kích thước ảnh bìa không được vượt quá :max KB.',
        ],
        'cover_image_url' => [
            'url' => 'URL ảnh bìa không hợp lệ.',
        ],
        'tag' => [
            'max' => 'Tag không được vượt quá :max ký tự.',
        ],
        'excerpt' => [
            'max' => 'Mô tả ngắn không được vượt quá :max ký tự.',
        ],
        'image' => [
            'image' => 'Tệp tải lên phải là hình ảnh.',
            'mimes' => 'Hình ảnh phải có định dạng: :values.',
            'max' => 'Kích thước hình ảnh không được vượt quá :max KB.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'title' => 'Tiêu đề',
        'name' => 'Tên',
        'content' => 'Nội dung',
        'author' => 'Tác giả',
        'author_name' => 'Tên tác giả',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'thumbnail' => 'Ảnh đại diện',
        'thumbnail_url' => 'URL ảnh đại diện',
        'cover_image' => 'Ảnh bìa',
        'cover_image_url' => 'URL ảnh bìa',
        'image' => 'Hình ảnh',
        'image_url' => 'URL hình ảnh',
        'tag' => 'Tag',
        'excerpt' => 'Mô tả ngắn',
        'description' => 'Mô tả',
        'order' => 'Thứ tự',
        'rating' => 'Đánh giá',
        'source' => 'Nguồn',
        'category_ids' => 'Thể loại',
        'published_year' => 'Năm xuất bản',
        'birth_year' => 'Năm sinh',
        'death_year' => 'Năm mất',
        'target_count' => 'Số lượng',
        'start_date' => 'Ngày bắt đầu',
        'end_date' => 'Ngày kết thúc',
        'badge_id' => 'Huy hiệu',
    ],

];
