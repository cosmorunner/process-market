<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * Class ExploreProcesses
 * @package App\Http\Resources
 */
class ExploreProcesses extends JsonResource {

    private $complexityScoreNumberOfBlocks;
    private $complexityScoreColors;
    /**
     * @var \App\Models\Process
     */
    public $resource;

    /**
     * @param $resource
     */
    public function __construct($resource) {
        $this->complexityScoreNumberOfBlocks = [
            1 => [0, 14],
            2 => [15, 24],
            3 => [25, 34],
            4 => [35, 44],
            5 => [45, 54],
            6 => [55, 64],
            7 => [65, 74],
            8 => [75, 84],
            9 => [85, 94],
            10 => [95, 100]
        ];
        $this->complexityScoreColors = [
            '#33a1f4' => [0, 34],
            '#dc57ff' => [35, 74],
            '#7756fa' => [75, 100]
        ];

        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $complexityScore = $this->resource->latestPublishedVersion->complexity_score;
        $complexityScoreToHundred = (int) ($complexityScore * 10);

        return [
            'short_title' => shorten($this->resource->title, 50),
            'short_description' => Str::limit((string) $this->resource->description, 250),
            'identifier' => $this->resource->identifier,
            'namespace' => $this->resource->namespace,
            'complexity_score' => [
                'color' => $this->getComplexityScoreColor($complexityScoreToHundred),
                'number_of_blocks' => $this->getComplexityScoreNumberOfBlocks($complexityScoreToHundred),
                'value' => max(as_decimal($complexityScore, 0), 1)
            ],
            'tags' => Tag::collection($this->resource->tags),
            'author_public_path' => $this->resource->author->publicPath(),
            'author_thumbnail_path' => $this->resource->author->thumbnailPath(),
            'public_path' => $this->resource->publicPath(),
            'has_open_source_license' => $this->resource->hasOpenSourceLicense()
        ];
    }

    /**
     * @param $score
     * @return int
     */
    private function getComplexityScoreNumberOfBlocks($score) {
        foreach ($this->complexityScoreNumberOfBlocks as $number => $range) {
            if (in_array($score, range(...$range))) {
                return $number;
            }
        }

        return 0;
    }

    /**
     * @param $score
     * @return string
     */
    private function getComplexityScoreColor($score) {
        foreach ($this->complexityScoreColors as $hex => $range) {
            if (in_array($score, range(...$range))) {
                return $hex;
            }
        }

        return '#ffffff';
    }
}
