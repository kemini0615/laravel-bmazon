<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bmazon</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <main class="mx-auto max-w-6xl px-6 py-16">
        <header class="rounded-3xl bg-slate-950 px-8 py-14 text-white shadow-xl">
            <p class="text-sm font-semibold uppercase text-amber-400">Bmazon</p>
            <h1 class="mt-4 text-4xl font-bold">홈 데이터 연결 완료</h1>
            <p class="mt-4 max-w-2xl text-slate-300">더미 홈 페이지.</p>
        </header>

        <section class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-2xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">추천 카테고리</p>
                <p class="mt-2 text-3xl font-bold">{{ $featuredCategories->count() }}</p>
            </article>
            <article class="rounded-2xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">활성 슬라이드</p>
                <p class="mt-2 text-3xl font-bold">{{ $sliders->count() }}</p>
            </article>
            <article class="rounded-2xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">인기 카테고리</p>
                <p class="mt-2 text-3xl font-bold">{{ $popularCategories->count() }}</p>
            </article>
            <article class="rounded-2xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">플래시세일 상품</p>
                <p class="mt-2 text-3xl font-bold">{{ $flashSaleProducts->count() }}</p>
            </article>
        </section>
    </main>
</body>
</html>
