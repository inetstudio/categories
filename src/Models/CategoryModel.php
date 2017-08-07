<?php

namespace InetStudio\Categories\Models;

use Cocur\Slugify\Slugify;
use Kalnoy\Nestedset\NodeTrait;
use Phoenix\EloquentMeta\MetaTrait;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Venturecraft\Revisionable\RevisionableTrait;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class CategoryModel extends Model implements HasMedia
{
    use MetaTrait;
    use SoftDeletes;
    use HasMediaTrait;
    use RevisionableTrait;
    use Sluggable, NodeTrait {
        NodeTrait::replicate as replicateNode;
        Sluggable::replicate as replicateSlug;
    }

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'description', 'content',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title',
                'unique' => true,
            ],
        ];
    }

    protected $revisionCreationsEnabled = true;

    /**
     * Правила для транслита.
     *
     * @param Slugify $engine
     * @param $attribute
     * @return Slugify
     */
    public function customizeSlugEngine(Slugify $engine, $attribute)
    {
        $engine->addRule('а', 'a');
        $engine->addRule('б', 'b');
        $engine->addRule('в', 'v');
        $engine->addRule('г', 'g');
        $engine->addRule('д', 'd');
        $engine->addRule('е', 'e');
        $engine->addRule('ё', 'jo');
        $engine->addRule('ж', 'zh');
        $engine->addRule('з', 'z');
        $engine->addRule('и', 'i');
        $engine->addRule('й', 'j');
        $engine->addRule('к', 'k');
        $engine->addRule('л', 'l');
        $engine->addRule('м', 'm');
        $engine->addRule('н', 'n');
        $engine->addRule('о', 'o');
        $engine->addRule('п', 'p');
        $engine->addRule('р', 'r');
        $engine->addRule('с', 's');
        $engine->addRule('т', 't');
        $engine->addRule('у', 'u');
        $engine->addRule('ф', 'f');
        $engine->addRule('х', 'h');
        $engine->addRule('ц', 'c');
        $engine->addRule('ч', 'ch');
        $engine->addRule('ш', 'sh');
        $engine->addRule('щ', 'shh');
        $engine->addRule('ъ', '');
        $engine->addRule('ы', 'y');
        $engine->addRule('ь', '');
        $engine->addRule('э', 'je');
        $engine->addRule('ю', 'ju');
        $engine->addRule('я', 'ja');

        return $engine;
    }

    public function replicate(array $except = null)
    {
        $instance = $this->replicateNode($except);
        (new SlugService())->slug($instance, true);

        return $instance;
    }

    public static function getTree()
    {
        $tree = self::defaultOrder()->get()->toTree();

        $data = [];

        $traverse = function ($categories) use (&$traverse, $data) {
            foreach ($categories as $category) {
                $data[$category->id]['id'] = $category->id;
                $data[$category->id]['name'] = $category->title;
                $data[$category->id]['items'] = $traverse($category->children);
            }

            return $data;
        };

        return $traverse($tree);
    }
}
