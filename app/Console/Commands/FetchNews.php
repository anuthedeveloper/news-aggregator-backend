<?php

namespace App\Console\Commands;

use App\Models\Article;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class FetchNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape latest news from APIs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting the news scraping process...');

        $sources = [
            'guardian' => 'https://content.guardianapis.com/search?page-size=50&api-key='.env('THE_GUARDIAN_API_KEY'),
            // 'nyt' => 'https://api.nytimes.com/svc/topstories/v2/home.json?api-key=',
            'nyt' => 'https://api.nytimes.com/svc/mostpopular/v2/emailed/7.json?api-key='.env('NEWYORK_API_KEY'),
            // 'opennews' => 'https://opennews.api/news?api-key=',
            'bbc' => 'https://newsapi.org/v2/everything?q=bbc-news&apiKey='.env('BBC_API_KEY')
        ];

        $client = new Client(['verify' => false]);

        foreach ($sources as $source => $url) {
            $response = $client->get($url);

            if ($response->getBody()) {
                $data = json_decode($response->getBody(), true);

                 // Parse articles based on the source structure
                 switch ($source) {
                    case 'guardian':
                        $this->parseGuardianArticles($data['response']['results']);
                        break;
                    case 'bbc':
                        $this->parseBBCArticles($data['articles']);
                        break;
                    case 'nyt':
                        $this->parseNYTArticles($data['results']);
                        break;
                }

            }
        }

        $this->info('News scraping process completed...');
    }

    private function parseGuardianArticles($articles)
    {
        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['url' => $article['webUrl']],
                [
                    'title' => $article['webTitle'],
                    'source' => 'The Guardian',
                    'author' => $article['author'] ?? 'Unknown',
                    'description' => $article['description'] ?? null,
                    'content' => $article['content'] ?? null,
                    'category' => $article['sectionName'] ?? 'General',
                    'url' => $article['webUrl'],
                    'url_to_image' => $article['webUrl'] ?? '',
                    'published_at' => Carbon::parse($article['webPublicationDate']),
                ]
            );
        }
    }

    private function parseBBCArticles($articles)
    {
        foreach ($articles as $article) {
            // Check if the article is from BBC News
            if (isset($article['source']['name']) && $article['source']['name'] === 'BBC News') {
                Article::updateOrCreate(
                    ['url' => $article['url']], // Unique constraint on URL
                    [
                        'title' => $article['title'],
                        'source' => 'BBC News',
                        'author' => $article['author'] ?? 'Unknown',
                        'description' => $article['description'],
                        'content' => $article['content'] ?? '',
                        'category' => $article['category'] ?? 'General',
                        'url' => $article['url'],
                        'url_to_image' => $article['urlToImage'],
                        'published_at' => Carbon::parse($article['publishedAt']),
                    ]
                );
            }
        }
    }


    private function parseNYTArticles($articles)
    {
        foreach ($articles as $article) {
            // Extract the main image if available
            $image = null;
            if (isset($article['multimedia']) && is_array($article['multimedia']) && count($article['multimedia']) > 0) {
                $image = $article['multimedia'][0]['url'];
            }
            
            Article::updateOrCreate(
                ['url' => $article['url']],
                [
                    'title' => $article['title'],
                    'source' => 'New York Times',
                    'author' => $article['byline'] ?? 'Unknown',
                    'description' => $article['abstract'],
                    'content' => $article['abstract'] ?? '',
                    'category' => $article['section'] ?? 'General',
                    'url' => $article['url'],
                    'url_to_image' => $image,
                    'published_at' => Carbon::parse($article['published_date']),
                ]
            );
        }
    }

}
