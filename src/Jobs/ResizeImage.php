<?php

namespace lagbox\Resizer\Jobs;

use lagbox\Resizer\Resizer;
use Illuminate\Bus\Queueable;
use lagbox\Resizer\Resizable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResizeImage implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Queueable;

    protected $resizable;

    public function __construct(Resizable $resizable)
    {
        $this->resizable = $resizable;
    }

    public function handle(Resizer $resizer)
    {
        $resizer->doIt($this->resizable);
    }
}
