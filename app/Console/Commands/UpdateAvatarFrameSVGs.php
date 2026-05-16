<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AvatarFrame;

class UpdateAvatarFrameSVGs extends Command
{
    protected $signature = 'frames:update-svgs';
    protected $description = 'Update all avatar frames with new beautiful SVG designs';

    public function handle()
    {
        $frames = [
            'khung-vang-hoang-gia' => [
                'name' => 'Khung Vàng Hoàng Gia',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"><defs><radialGradient id="g1" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="#ffe066"/><stop offset="60%" stop-color="#e6a800"/><stop offset="100%" stop-color="#b8860b"/></radialGradient></defs><circle cx="60" cy="60" r="58" fill="none" stroke="url(#g1)" stroke-width="5"/><circle cx="60" cy="60" r="50" fill="none" stroke="#ffe066" stroke-width="1.5" stroke-dasharray="4 3"/><circle cx="60" cy="60" r="46" fill="none" stroke="url(#g1)" stroke-width="3"/><polygon points="60,4 63,10 60,7 57,10" fill="#ffd700"/><polygon points="60,116 63,110 60,113 57,110" fill="#ffd700"/><polygon points="4,60 10,57 7,60 10,63" fill="#ffd700"/><polygon points="116,60 110,57 113,60 110,63" fill="#ffd700"/><circle cx="60" cy="60" r="42" fill="none" stroke="#ffe066" stroke-width="0.5" stroke-dasharray="2 4"/></svg>',
            ],
            'khung-mua-xuan-ruc-ro' => [
                'name' => 'Khung Mùa Xuân Rực Rỡ',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"><circle cx="60" cy="60" r="56" fill="none" stroke="#ff9eb5" stroke-width="4"/><circle cx="60" cy="60" r="50" fill="none" stroke="#ffccd5" stroke-width="2"/><g transform="translate(60,8)"><ellipse cx="-3" cy="-3" rx="4" ry="6" fill="#ff85a1"/><ellipse cx="3" cy="-3" rx="4" ry="6" fill="#ff85a1"/><ellipse cx="-5" cy="2" rx="4" ry="6" fill="#ffadc5" transform="rotate(30,-5,2)"/><ellipse cx="5" cy="2" rx="4" ry="6" fill="#ffadc5" transform="rotate(-30,5,2)"/><circle cx="0" cy="0" r="2.5" fill="#ffd700"/></g><g transform="translate(60,112) rotate(180)"><ellipse cx="-3" cy="-3" rx="4" ry="6" fill="#ff85a1"/><ellipse cx="3" cy="-3" rx="4" ry="6" fill="#ff85a1"/><circle cx="0" cy="0" r="2.5" fill="#ffd700"/></g><g transform="translate(8,60) rotate(270)"><ellipse cx="-3" cy="-3" rx="4" ry="6" fill="#ff85a1"/><ellipse cx="3" cy="-3" rx="4" ry="6" fill="#ff85a1"/><circle cx="0" cy="0" r="2.5" fill="#ffd700"/></g><g transform="translate(112,60) rotate(90)"><ellipse cx="-3" cy="-3" rx="4" ry="6" fill="#ff85a1"/><ellipse cx="3" cy="-3" rx="4" ry="6" fill="#ff85a1"/><circle cx="0" cy="0" r="2.5" fill="#ffd700"/></g><path d="M 20 25 Q 28 18 35 25" stroke="#66bb6a" stroke-width="2" fill="none"/><path d="M 85 25 Q 92 18 100 25" stroke="#66bb6a" stroke-width="2" fill="none"/><path d="M 20 95 Q 28 102 35 95" stroke="#66bb6a" stroke-width="2" fill="none"/><path d="M 85 95 Q 92 102 100 95" stroke="#66bb6a" stroke-width="2" fill="none"/></svg>',
            ],
            'khung-dau-bep-chuyen-nghiep' => [
                'name' => 'Khung Đầu Bếp Chuyên Nghiệp',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"><circle cx="60" cy="60" r="57" fill="none" stroke="#2c3e50" stroke-width="4"/><circle cx="60" cy="60" r="51" fill="none" stroke="#e67e22" stroke-width="2"/><path d="M 54 10 Q 60 2 66 10 Q 72 6 73 13 Q 78 12 78 18 Q 78 22 73 23 L 47 23 Q 42 22 42 18 Q 42 12 47 13 Q 48 6 54 10Z" fill="white" stroke="#2c3e50" stroke-width="1.5"/><rect x="46" y="23" width="28" height="4" rx="1" fill="#e8e8e8" stroke="#2c3e50" stroke-width="1"/><g transform="translate(20,48)"><line x1="0" y1="0" x2="0" y2="22" stroke="#2c3e50" stroke-width="2.5"/><line x1="-3" y1="0" x2="-3" y2="10" stroke="#2c3e50" stroke-width="2"/><line x1="3" y1="0" x2="3" y2="10" stroke="#2c3e50" stroke-width="2"/></g><g transform="translate(100,48)"><ellipse cx="0" cy="5" rx="4" ry="5.5" fill="none" stroke="#2c3e50" stroke-width="2"/><line x1="0" y1="10" x2="0" y2="28" stroke="#2c3e50" stroke-width="2.5"/></g><circle cx="60" cy="97" r="3" fill="#e67e22"/><circle cx="60" cy="60" r="47" fill="none" stroke="#e67e22" stroke-width="0.5" opacity="0.5"/></svg>',
            ],
            'khung-kim-cuong-lap-lanh' => [
                'name' => 'Khung Kim Cương Lấp Lánh',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"><defs><linearGradient id="dg1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#b9f2ff"/><stop offset="50%" stop-color="#00bcd4"/><stop offset="100%" stop-color="#0097a7"/></linearGradient></defs><circle cx="60" cy="60" r="57" fill="none" stroke="url(#dg1)" stroke-width="4"/><circle cx="60" cy="60" r="51" fill="none" stroke="#4fc3f7" stroke-width="1.5" stroke-dasharray="6 4"/><polygon points="60,5 65,12 60,9 55,12" fill="#4fc3f7"/><polygon points="60,115 65,108 60,111 55,108" fill="#4fc3f7"/><polygon points="5,60 12,55 9,60 12,65" fill="#4fc3f7"/><polygon points="115,60 108,55 111,60 108,65" fill="#4fc3f7"/><path d="M 22 22 L 24 26 L 22 28 L 20 26Z" fill="#4fc3f7"/><path d="M 98 22 L 100 26 L 98 28 L 96 26Z" fill="#4fc3f7"/><path d="M 22 98 L 24 94 L 22 92 L 20 94Z" fill="#4fc3f7"/><path d="M 98 98 L 100 94 L 98 92 L 96 94Z" fill="#4fc3f7"/><path d="M 60 2 L 61 5 L 64 4 L 62 7 L 65 8 L 62 9 L 63 12 L 60 10 L 57 12 L 58 9 L 55 8 L 58 7 L 56 4 L 59 5Z" fill="#b9f2ff" opacity="0.8"/></svg>',
            ],
            'khung-go-moc-mac' => [
                'name' => 'Khung Gỗ Mộc Mạc',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"><circle cx="60" cy="60" r="57" fill="none" stroke="#6b3a1f" stroke-width="6"/><circle cx="60" cy="60" r="54" fill="none" stroke="#c68642" stroke-width="3"/><path d="M 20 35 Q 45 30 80 38 Q 95 40 102 38" stroke="#c68642" stroke-width="1" fill="none" opacity="0.5"/><path d="M 18 55 Q 50 48 85 56 Q 98 58 104 56" stroke="#a0522d" stroke-width="1" fill="none" opacity="0.5"/><path d="M 18 75 Q 50 80 85 72 Q 98 70 104 72" stroke="#c68642" stroke-width="1" fill="none" opacity="0.5"/><g transform="translate(19,19) rotate(-45)"><path d="M 0 0 Q 5 -8 10 0 Q 5 8 0 0Z" fill="#7cb342" opacity="0.8"/></g><g transform="translate(101,19) rotate(45)"><path d="M 0 0 Q 5 -8 10 0 Q 5 8 0 0Z" fill="#7cb342" opacity="0.8"/></g><g transform="translate(19,101) rotate(135)"><path d="M 0 0 Q 5 -8 10 0 Q 5 8 0 0Z" fill="#7cb342" opacity="0.8"/></g><g transform="translate(101,101) rotate(225)"><path d="M 0 0 Q 5 -8 10 0 Q 5 8 0 0Z" fill="#7cb342" opacity="0.8"/></g><circle cx="60" cy="60" r="47" fill="none" stroke="#8d5524" stroke-width="1.5"/></svg>',
            ],
            'khung-vinh-quang-vang' => [
                'name' => 'Khung Vinh Quang Vàng',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"><defs><linearGradient id="gg1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#ff8c00"/><stop offset="50%" stop-color="#ffd700"/><stop offset="100%" stop-color="#ff8c00"/></linearGradient></defs><circle cx="60" cy="60" r="56" fill="none" stroke="url(#gg1)" stroke-width="5"/><circle cx="60" cy="60" r="49" fill="none" stroke="#ffd700" stroke-width="1" stroke-dasharray="3 2"/><path d="M 14 45 Q 20 38 28 44 Q 22 50 14 45Z" fill="#388e3c"/><path d="M 12 57 Q 17 49 26 54 Q 20 62 12 57Z" fill="#4caf50"/><path d="M 13 70 Q 19 62 28 67 Q 22 75 13 70Z" fill="#388e3c"/><path d="M 106 45 Q 100 38 92 44 Q 98 50 106 45Z" fill="#388e3c"/><path d="M 108 57 Q 103 49 94 54 Q 100 62 108 57Z" fill="#4caf50"/><path d="M 107 70 Q 101 62 92 67 Q 98 75 107 70Z" fill="#388e3c"/><path d="M 60 6 L 62 11 L 67 11 L 63 14 L 65 19 L 60 16 L 55 19 L 57 14 L 53 11 L 58 11Z" fill="#ffd700"/><path d="M 40 105 Q 60 110 80 105 L 78 112 Q 60 115 42 112Z" fill="url(#gg1)"/></svg>',
            ],
            'khung-hoa-long' => [
                'name' => 'Khung Hỏa Long',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"><circle cx="60" cy="60" r="57" fill="none" stroke="#b71c1c" stroke-width="5"/><circle cx="60" cy="60" r="51" fill="none" stroke="#ff5722" stroke-width="2"/><path d="M 57 8 Q 60 2 63 8 Q 65 4 67 9 Q 64 7 62 12 Q 60 8 58 12 Q 56 7 53 9 Q 55 4 57 8Z" fill="#ff5722"/><path d="M 57 8 Q 60 4 63 8 Q 61 6 60 10 Q 59 6 57 8Z" fill="#ffd740"/><path d="M 57 112 Q 60 118 63 112 Q 65 116 67 111 Q 64 113 62 108 Q 60 112 58 108 Q 56 113 53 111 Q 55 116 57 112Z" fill="#ff5722"/><path d="M 8 57 Q 2 60 8 63 Q 4 65 9 67 Q 7 64 12 62 Q 8 60 12 58 Q 7 56 9 53 Q 4 55 8 57Z" fill="#ff5722"/><path d="M 112 57 Q 118 60 112 63 Q 116 65 111 67 Q 113 64 108 62 Q 112 60 108 58 Q 113 56 111 53 Q 116 55 112 57Z" fill="#ff5722"/><path d="M 30 18 Q 35 14 40 18" stroke="#ff5722" stroke-width="2" fill="none"/><path d="M 80 18 Q 85 14 90 18" stroke="#ff5722" stroke-width="2" fill="none"/><path d="M 30 102 Q 35 106 40 102" stroke="#ff5722" stroke-width="2" fill="none"/><path d="M 80 102 Q 85 106 90 102" stroke="#ff5722" stroke-width="2" fill="none"/><circle cx="60" cy="60" r="46" fill="none" stroke="#ff5722" stroke-width="0.5" opacity="0.5"/></svg>',
            ],
            'khung-kim-cuong-bat-diet' => [
                'name' => 'Khung Kim Cương Bất Diệt',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"><defs><linearGradient id="pg1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#e8eaf6"/><stop offset="25%" stop-color="#9c27b0"/><stop offset="50%" stop-color="#e8eaf6"/><stop offset="75%" stop-color="#7b1fa2"/><stop offset="100%" stop-color="#e8eaf6"/></linearGradient></defs><circle cx="60" cy="60" r="57" fill="none" stroke="url(#pg1)" stroke-width="5"/><circle cx="60" cy="60" r="51" fill="none" stroke="#e040fb" stroke-width="2" stroke-dasharray="8 3"/><polygon points="60,5 65,12 60,18 55,12" fill="#e040fb" stroke="#f8bbd9" stroke-width="0.5"/><polygon points="60,115 65,108 60,102 55,108" fill="#e040fb"/><polygon points="5,60 12,55 18,60 12,65" fill="#e040fb"/><polygon points="115,60 108,55 102,60 108,65" fill="#e040fb"/><path d="M 22 22 L 24 18 L 26 22 L 30 24 L 26 26 L 24 30 L 22 26 L 18 24Z" fill="#e040fb" opacity="0.9"/><path d="M 98 22 L 100 18 L 102 22 L 106 24 L 102 26 L 100 30 L 98 26 L 94 24Z" fill="#e040fb" opacity="0.9"/><path d="M 22 98 L 24 94 L 26 98 L 30 100 L 26 102 L 24 106 L 22 102 L 18 100Z" fill="#e040fb" opacity="0.9"/><path d="M 98 98 L 100 94 L 102 98 L 106 100 L 102 102 L 100 106 L 98 102 L 94 100Z" fill="#e040fb" opacity="0.9"/><circle cx="60" cy="60" r="46" fill="none" stroke="#e040fb" stroke-width="0.5" opacity="0.4"/></svg>',
            ],
            'khung-huyen-thoai-legend' => [
                'name' => 'Khung Huyền Thoại (Legend)',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"><defs><linearGradient id="lg1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#1a237e"/><stop offset="33%" stop-color="#6a1b9a"/><stop offset="66%" stop-color="#1a237e"/><stop offset="100%" stop-color="#0d47a1"/></linearGradient></defs><circle cx="60" cy="60" r="57" fill="none" stroke="url(#lg1)" stroke-width="5"/><circle cx="60" cy="7" r="2" fill="#ffe57f"/><circle cx="95" cy="20" r="1.5" fill="#b39ddb"/><circle cx="110" cy="50" r="1.5" fill="#ffe57f"/><circle cx="108" cy="80" r="1.5" fill="#b39ddb"/><circle cx="85" cy="108" r="2" fill="#ffe57f"/><circle cx="35" cy="108" r="1.5" fill="#b39ddb"/><circle cx="12" cy="80" r="1.5" fill="#ffe57f"/><circle cx="10" cy="50" r="1.5" fill="#b39ddb"/><circle cx="25" cy="20" r="2" fill="#ffe57f"/><line x1="60" y1="7" x2="95" y2="20" stroke="#9575cd" stroke-width="0.5" opacity="0.6"/><line x1="95" y1="20" x2="110" y2="50" stroke="#9575cd" stroke-width="0.5" opacity="0.6"/><line x1="60" y1="7" x2="25" y2="20" stroke="#9575cd" stroke-width="0.5" opacity="0.6"/><circle cx="60" cy="60" r="51" fill="none" stroke="#7c4dff" stroke-width="1.5" stroke-dasharray="5 4"/><circle cx="60" cy="60" r="46" fill="none" stroke="#1a237e" stroke-width="1" opacity="0.5"/><path d="M 60 50 L 62 56 L 68 56 L 63 60 L 65 66 L 60 62 L 55 66 L 57 60 L 52 56 L 58 56Z" fill="#ffe57f" opacity="0.5"/></svg>',
            ],
            'khung-hoang-gia-toi-cao' => [
                'name' => 'Khung Hoàng Gia Tối Cao',
                'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"><circle cx="60" cy="60" r="58" fill="none" stroke="#7f0000" stroke-width="4"/><circle cx="60" cy="60" r="54" fill="none" stroke="#ffd700" stroke-width="2"/><circle cx="60" cy="4" r="4" fill="#ffd700" stroke="#b8860b" stroke-width="1"/><circle cx="60" cy="116" r="4" fill="#ffd700" stroke="#b8860b" stroke-width="1"/><circle cx="4" cy="60" r="3" fill="#dc143c" stroke="#8b0000" stroke-width="1"/><circle cx="116" cy="60" r="3" fill="#dc143c" stroke="#8b0000" stroke-width="1"/><path d="M 46 16 L 50 8 L 55 14 L 60 6 L 65 14 L 70 8 L 74 16 L 72 22 L 48 22Z" fill="#ffd700" stroke="#b8860b" stroke-width="1"/><circle cx="50" cy="16" r="2" fill="#dc143c"/><circle cx="60" cy="13" r="2.5" fill="#dc143c"/><circle cx="70" cy="16" r="2" fill="#dc143c"/><circle cx="60" cy="60" r="48" fill="none" stroke="#b71c1c" stroke-width="3"/><circle cx="60" cy="60" r="44" fill="none" stroke="#ffd700" stroke-width="1" stroke-dasharray="4 3"/><path d="M 28 98 Q 44 92 60 95 Q 76 92 92 98" stroke="#ffd700" stroke-width="2" fill="none"/><path d="M 20 35 Q 28 28 35 35" stroke="#ffd700" stroke-width="1.5" fill="none"/><path d="M 85 35 Q 92 28 100 35" stroke="#ffd700" stroke-width="1.5" fill="none"/><path d="M 20 85 Q 28 92 35 85" stroke="#ffd700" stroke-width="1.5" fill="none"/><path d="M 85 85 Q 92 92 100 85" stroke="#ffd700" stroke-width="1.5" fill="none"/></svg>',
            ],
        ];

        foreach ($frames as $slug => $data) {
            $frame = AvatarFrame::where('slug', $slug)->first();
            if ($frame) {
                $encoded = 'data:image/svg+xml;utf8,' . rawurlencode($data['svg']);
                $frame->frame_image = $encoded;
                $frame->save();
                $this->info("✅ Updated: {$data['name']}");
            } else {
                $this->warn("❌ Not found: $slug");
            }
        }

        $this->info('All frames updated!');
    }
}
