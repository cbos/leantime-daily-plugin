<?php

namespace Leantime\Plugins\Daily\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Leantime\Core\Configuration\Environment;
use Leantime\Core\Http\IncomingRequest;
use Symfony\Component\HttpFoundation\Response;
use Leantime\Core\Language;
use Illuminate\Support\Facades\Log;

class GetLanguageAssets
{
    public function __construct(
        private Language $language,
        private Environment $config,
    ) {}

    /**
     * Install the custom fields plugin DB if necessary.
     *
     * @param \Closure(IncomingRequest): Response $next
     * @throws BindingResolutionException
     **/
    public function handle(IncomingRequest $request, Closure $next): Response
    {
        Log::error("handle request to get the language");
        $languageArray = Cache::get('daily.languageArray', []);

        if (! empty($languageArray)) {
            $this->language->ini_array = array_merge($this->language->ini_array, $languageArray);
            return $next($request);
        }

        if (! Cache::store('installation')->has('daily.language.en-US')) {
            $languageArray += parse_ini_file(__DIR__ . '/../Language/en-US.ini', true);
        }

        if (($language = $_SESSION["usersettings.language"] ?? $this->config->language) !== 'en-US') {
            if (! Cache::store('installation')->has('daily.language.' . $language)) {
                Cache::store('installation')->put(
                    'daily.language.' . $language,
                    parse_ini_file(__DIR__ . '/../Language/' . $language . '.ini', true)
                );
            }

            $languageArray = array_merge($languageArray, Cache::store('installation')->get('daily.language.' . $language));
        }

        Cache::put('daily.languageArray', $languageArray);

        $this->language->ini_array = array_merge($this->language->ini_array, $languageArray);
        return $next($request);
    }
}

