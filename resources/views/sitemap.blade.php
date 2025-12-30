<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Homepage -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Channels -->
    @foreach($channels as $channel)
    <url>
        <loc>{{ route('channel.show', $channel) }}</loc>
        <lastmod>{{ $channel->updated_at->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    <!-- Discussions -->
    @foreach($discussions as $discussion)
    <url>
        <loc>{{ route('discussions.show', $discussion) }}</loc>
        <lastmod>{{ $discussion->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
</urlset>
