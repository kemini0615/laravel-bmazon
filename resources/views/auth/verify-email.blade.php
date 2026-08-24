<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>이메일 인증 | {{ config('app.name', 'Bmazon') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <section class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl shadow-slate-200 sm:p-10">
            <div class="text-center">
                <p class="mb-3 text-sm font-semibold uppercase text-indigo-600">Bmazon</p>
                <h1 class="text-3xl font-bold tracking-tight">이메일 인증</h1>
                <p class="mt-4 text-sm leading-6 text-slate-500">
                    계정 사용을 계속하려면 이메일 인증이 필요합니다. 아래 버튼을 눌러 인증 링크를 받은 뒤 이메일에서 링크를 열어주세요
                </p>
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="mt-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                    이메일 인증 링크를 전송했습니다
                </div>
            @endif

            <div class="mt-8 space-y-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200"
                    >
                        인증 이메일 다시 보내기
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200"
                    >
                        로그아웃
                    </button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
