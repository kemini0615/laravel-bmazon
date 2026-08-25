<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>비밀번호 재확인 | {{ config('app.name', 'Bmazon') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <section class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl shadow-slate-200 sm:p-10">
            <div class="mb-8 text-center">
                <p class="mb-3 text-sm font-semibold uppercase text-indigo-600">Bmazon</p>
                <h1 class="text-3xl font-bold tracking-tight">비밀번호 재확인</h1>
                <p class="mt-3 text-sm leading-6 text-slate-500">
                    민감한 작업을 계속하기 전에 현재 비밀번호를 다시 입력해주세요
                </p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">현재 비밀번호</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autofocus
                        autocomplete="current-password"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        placeholder="현재 비밀번호"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200"
                >
                    확인
                </button>
            </form>
        </section>
    </main>
</body>
</html>
