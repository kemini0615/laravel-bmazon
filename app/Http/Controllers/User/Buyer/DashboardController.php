<?php

namespace App\Http\Controllers\User\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // 1. 현재 로그인 사용자가 Buyer인지 확인한다
    // 2. Seller라면 Seller 대시보드로 이동시킨다
    // 3. Buyer라면 주문과 계정 기능을 추가하기 전의 최소 대시보드를 출력한다
    public function index(Request $request): RedirectResponse|View
    {
        // user()는 auth 미들웨어가 인증한 현재 User 모델을 반환해 user_type을 확인하게 한다
        $user = $request->user();

        // TODO: 미들웨어로 교체한다
        if ($user->user_type !== 'buyer') {
            return redirect()->route('seller.dashboard');
        }

        return view('user.buyer.dashboard.index', ['user' => $user]);
    }
}
