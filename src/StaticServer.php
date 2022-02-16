<?php
declare(strict_types=1);

namespace Mvc4us\ReactStatic;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class StaticServer
{

    private array $path;

    private array $exclude;

    /**
     * @param array $path Associated array of url base paths as keys and absolute directory paths as values to serve static files from.
     * @param array $exclude Array of shell patterns for file exclusion.
     */
    public function __construct(array $path, array $exclude = [])
    {
        $this->path = $path;
        $this->exclude = $exclude;
    }

    public function __invoke(ServerRequestInterface $request, callable $next): Response
    {
        if (empty($this->path)) {
            return $next($request);
        }

        foreach ($this->path as $urlBase => $path) {
            $urlPath = $request->getUri()->getPath();
            if ($urlBase !== '/') {
                if (!str_starts_with($urlPath, $urlBase)) {
                    continue;
                }
                $urlPath = str_replace($urlBase, '', $urlPath);
            }
            $file = $path . $urlPath;

            if (file_exists($file) && !is_dir($file)) {
                if ($this->isExcluded($file)) {
                    return new Response(404);
                }
                $fileExt = pathinfo($file, PATHINFO_EXTENSION);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $fileType = finfo_file($finfo, $file);
                finfo_close($finfo);
                $fileContents = file_get_contents($file);

                // Fix for incorrect mime types
                switch ($fileExt) {
                    case 'css':
                        $fileType = 'text/css';

                        break;
                    case 'js':
                        $fileType = 'application/javascript';

                        break;
                }

                return new Response(200, ['Content-Type' => $fileType], $fileContents);
            }
        }

        return $next($request);
    }

    private function isExcluded($file): bool
    {
        foreach ($this->exclude as $pattern) {
            if (fnmatch($pattern, basename($file))) {
                return true;
            }
        }
        return false;
    }
}
