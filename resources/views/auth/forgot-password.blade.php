<!DOCTYPE html>
{{-- app()->getLocale()은 현재 애플리케이션 locale을 읽어 HTML 언어 속성에 반영한다 --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- config()는 config/app.php의 애플리케이션 이름을 읽는다 --}}
    <title>비밀번호 찾기 | {{ config('app.name', 'Bmazon') }}</title>
    {{-- @vite는 Vite가 빌드한 CSS와 JavaScript 자산을 페이지에 연결한다 --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <section class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl shadow-slate-200 sm:p-10">
            <div class="mb-8 text-center">
                <p class="mb-3 text-sm font-semibold uppercase text-indigo-600">Bmazon</p>
                <h1 class="text-3xl font-bold tracking-tight">비밀번호 찾기</h1>
                <p class="mt-3 text-sm leading-6 text-slate-500">
                    가입한 이메일 주소를 입력하면 비밀번호를 다시 설정할 수 있는 링크를 보내드립니다
                </p>
            </div>

            {{-- session('status')는 재설정 링크 전송 결과를 다음 요청까지 저장한 세션 플래시 메시지를 읽는다 --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                {{-- @csrf는 세션의 CSRF 토큰을 폼에 넣어 위조된 POST 요청을 막는다 --}}
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">이메일</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        {{-- old()는 링크 전송 실패 후 세션에 잠시 저장된 이전 이메일을 다시 보여준다 --}}
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        placeholder="you@example.com"
                    >
                    {{-- @error는 세션의 오류에서 email 필드를 찾아 오류 메시지를 출력한다 --}}
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200"
                >
                    재설정 링크 보내기
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                {{-- route()는 URL을 직접 작성하지 않고 이름이 login인 라우트의 URL을 생성한다 --}}
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">로그인으로 돌아가기</a>
            </p>
        </section>
    </main>
</body>
</html>
