<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // WithoutModelEvents Trait은 Seeder 실행 중 모델 이벤트와 Listener가 불필요하게 실행되는 것을 막는다
    use WithoutModelEvents;

    // 1. 학습용 Buyer와 Seller 계정 정보를 정의한다
    // 2. 이메일을 기준으로 기존 사용자를 갱신하거나 새 사용자를 만든다
    // 3. 두 계정의 이메일 인증 시각을 현재 시각으로 저장한다
    public function run(): void
    {
        $users = [
            [
                'name' => 'buyer',
                'email' => 'buyer@test.com',
                'password' => 'buyertest',
                'user_type' => 'buyer',
            ],
            [
                'name' => 'seller',
                'email' => 'seller@test.com',
                'password' => 'sellertest',
                'user_type' => 'seller',
            ],
        ];

        foreach ($users as $attributes) {
            // updateOrCreate()는 email이 같은 User가 있으면 갱신하고, 없으면 생성해 Seeder 재실행 시 중복 계정이 생기지 않게 한다
            // User의 hashed cast는 평문 password를 데이터베이스에 저장하기 전에 자동으로 해시한다
            $user = User::updateOrCreate(
                ['email' => $attributes['email']],
                [
                    'name' => $attributes['name'],
                    'password' => $attributes['password'],
                    'user_type' => $attributes['user_type'],
                ],
            );

            // forceFill()은 $fillable에 없는 email_verified_at을 Seeder가 명시적으로 채우기 위해 대량 할당 제한을 우회한다
            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();
        }
    }
}
