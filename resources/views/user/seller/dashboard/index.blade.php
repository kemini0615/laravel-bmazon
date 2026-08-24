<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller 대시보드 | {{ config('app.name', 'Bmazon') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto flex min-h-screen max-w-5xl items-center px-4 py-12">
        <section class="w-full rounded-3xl bg-white p-8 shadow-xl shadow-slate-200 sm:p-10">
            <p class="text-sm font-semibold uppercase text-indigo-600">Bmazon Seller</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight">{{ $user->name }}님의 대시보드</h1>
            <p class="mt-4 max-w-2xl leading-7 text-slate-500">
                상점, 상품, 주문 기능은 Seller 도메인에서 순서대로 추가됩니다
            </p>

            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf

                <button type="submit" class="rounded-xl border border-slate-300 px-4 py-3 font-semibold text-slate-700 transition hover:bg-slate-50">
                    로그아웃
                </button>
            </form>
        </section>
    </main>
</body>
</html>
