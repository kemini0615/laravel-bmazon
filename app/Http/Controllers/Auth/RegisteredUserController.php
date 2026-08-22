<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    // 1. 회원가입 화면을 출력한다
    public function create(): View
    {
        return view('auth.register');
    }

    // 1. 회원가입 요청의 이름, 이메일, 비밀번호, 사용자 유형을 검증한다
    // 2. 비밀번호를 해시하여 새 User 모델을 생성한다
    // 3. Registered 이벤트를 발생시켜 이메일 인증 등 후속 기능이 동작할 수 있게 한다
    // 4. 생성한 사용자를 즉시 로그인시킨다
    // 5. 사용자 유형에 따라 Seller 또는 Buyer 대시보드로 이동시킨다
    public function store(Request $request): RedirectResponse
    {
        // validate()는 검증 실패 시 오류와 이전 입력값을 세션 플래시에 저장하고 이전 화면으로 돌아간다
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => ['required', 'in:buyer,seller'],
        ]);

        // User::create()는 $fillable에 허용된 필드를 사용해 users 테이블에 새 사용자를 저장한다
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // Hash::make()는 평문 비밀번호를 데이터베이스에 저장하기 전에 해시로 변환한다
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        // event()는 Registered 이벤트를 dispatch해 이 이벤트를 구독하는 Listener가 실행될 수 있게 한다
        // 현재 User는 MustVerifyEmail을 구현하지 않았으므로 기본 Listener가 이메일 인증 알림을 보내지는 않는다
        event(new Registered($user));

        // Auth::login()은 방금 생성한 사용자를 즉시 로그인 상태로 만든다
        Auth::login($user);

        if (auth('web')->user()->user_type === 'seller') {
            return redirect(route('seller.dashboard', absolute: false));
        }

        return redirect(route('dashboard', absolute: false));
    }
}
