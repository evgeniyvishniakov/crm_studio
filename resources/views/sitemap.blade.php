{{-- Sitemap XML --}}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    
    <!-- Главная страница -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toISOString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
        <xhtml:link rel="alternate" hreflang="ru" href="{{ url('/?lang=ru') }}"/>
        <xhtml:link rel="alternate" hreflang="en" href="{{ url('/?lang=en') }}"/>
        <xhtml:link rel="alternate" hreflang="ua" href="{{ url('/?lang=ua') }}"/>
    </url>
    
    <!-- Страница блога -->
    <url>
        <loc>{{ route('beautyflow.blog.fallback') }}</loc>
        <lastmod>{{ now()->toISOString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
        <xhtml:link rel="alternate" hreflang="ru" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog', [], 'ru') }}"/>
        <xhtml:link rel="alternate" hreflang="en" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog', [], 'en') }}"/>
        <xhtml:link rel="alternate" hreflang="ua" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog', [], 'ua') }}"/>
    </url>
    
    <!-- Статьи блога -->
    @foreach($articles as $article)
    <url>
        <loc>{{ route('beautyflow.blog.show.fallback', $article->slug) }}</loc>
        <lastmod>{{ $article->updated_at->toISOString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
        <xhtml:link rel="alternate" hreflang="ru" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog.show', ['slug' => $article->slug], 'ru') }}"/>
        <xhtml:link rel="alternate" hreflang="en" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog.show', ['slug' => $article->slug], 'en') }}"/>
        <xhtml:link rel="alternate" hreflang="ua" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog.show', ['slug' => $article->slug], 'ua') }}"/>
    </url>
    @endforeach
    
    <!-- Категории блога -->
    @foreach($categories as $category)
    <url>
        <loc>{{ route('beautyflow.blog.fallback') }}?category={{ $category->id }}</loc>
        <lastmod>{{ $category->updated_at->toISOString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
        <xhtml:link rel="alternate" hreflang="ru" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog', [], 'ru') }}?category={{ $category->id }}"/>
        <xhtml:link rel="alternate" hreflang="en" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog', [], 'en') }}?category={{ $category->id }}"/>
        <xhtml:link rel="alternate" hreflang="ua" href="{{ \App\Helpers\LanguageHelper::createSeoUrl('beautyflow.blog', [], 'ua') }}?category={{ $category->id }}"/>
    </url>
    @endforeach
    
</urlset>
