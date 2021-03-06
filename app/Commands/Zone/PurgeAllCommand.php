<?php

namespace App\Commands\Zone;

use Cloudflare\API\Endpoints\Zones;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class PurgeAllCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'zone:purge-all';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove ALL files from Cloudflare\'s cache, for every Website';

    /**
     * Execute the console command.
     *
     * @param \Cloudflare\API\Endpoints\Zones $zones
     *
     * @return mixed
     */
    public function handle(Zones $zones)
    {
        if (! $this->output->getFormatter()->hasStyle('fail')) {
            $style = new OutputFormatterStyle('red');

            $this->output->getFormatter()->setStyle('fail', $style);
        }

        $this->output->title($this->description);

        collect($zones->listZones()->result)
            ->each(function ($zone) use ($zones): void {
                try {
                    $status = $zones->cachePurgeEverything($zone->id);
                } catch (\GuzzleHttp\Exception\ServerException $exception) {
                    $status = false;
                }

                $this->task(sprintf('Cache purge for %s ', $zone->name), function () use ($status) {
                    return $status;
                });
            });
    }
}
