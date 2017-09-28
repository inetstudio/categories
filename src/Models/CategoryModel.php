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
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use InetStudio\SimpleCounters\Traits\HasSimpleCountersTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

/**
 * InetStudio\Categories\Models\CategoryModel
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string|null $content
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $parent_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Kalnoy\Nestedset\Collection|\InetStudio\Categories\Models\CategoryModel[] $children
 * @property-read \Illuminate\Contracts\Routing\UrlGenerator|string $href
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @property-read \Illuminate\Database\Eloquent\Collection|\Phoenix\EloquentMeta\Meta[] $meta
 * @property-read \InetStudio\Categories\Models\CategoryModel|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel d()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel findSimilarSlugs(\Illuminate\Database\Eloquent\Model $model, $attribute, $config, $slug)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Categories\Models\CategoryModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereLft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereRgt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Categories\Models\CategoryModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Categories\Models\CategoryModel withoutTrashed()
 * @mixin \Eloquent
 */
class CategoryModel extends Model implements HasMediaConversions
{
    use MetaTrait;
    use SoftDeletes;
    use HasMediaTrait;
    use RevisionableTrait;
    use Sluggable, NodeTrait {
        NodeTrait::replicate as replicateNode;
        Sluggable::replicate as replicateSlug;
    }
    use SluggableScopeHelpers;
    use HasSimpleCountersTrait;

    const HREF = '/category/';

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

    protected $revisionCreationsEnabled = true;

    /**
     * Возвращаем конфиг для генерации slug модели.
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

    /**
     * Правила для транслита.
     *
     * @param Slugify $engine
     * @return Slugify
     */
    public function customizeSlugEngine(Slugify $engine)
    {
        $rules = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
            'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'je', 'ю' => 'ju', 'я' => 'ja',
        ];

        $engine->addRules($rules);

        return $engine;
    }

    /**
     * Ссылка на категорию.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getHrefAttribute()
    {
        return url(self::HREF . (!empty($this->slug) ? $this->slug : $this->id));
    }

    public function replicate(array $except = null)
    {
        $instance = $this->replicateNode($except);
        (new SlugService())->slug($instance, true);

        return $instance;
    }

    /**
     * Получаем дерево категорий.
     *
     * @return array
     */
    public static function getTree()
    {
        $tree = self::defaultOrder()->get()->toTree();

        $data = [];

        $traverse = function ($categories) use (&$traverse, $data) {
            foreach ($categories as $category) {
                $data[$category->id]['id'] = $category->id;
                $data[$category->id]['name'] = $category->title;
                $data[$category->id]['href'] = $category->href;
                $data[$category->id]['items'] = $traverse($category->children);
            }

            return $data;
        };

        return $traverse($tree);
    }

    /**
     * Регистрируем преобразования изображений.
     */
    public function registerMediaConversions()
    {
        $quality = (config('categories.images.quality')) ? config('categories.images.quality') : 75;

        if (config('categories.images.conversions')) {
            foreach (config('categories.images.conversions') as $collection => $image) {
                foreach ($image as $crop) {
                    foreach ($crop as $conversion) {
                        $imageConversion = $this->addMediaConversion($conversion['name']);

                        if (isset($conversion['size']['width'])) {
                            $imageConversion->width($conversion['size']['width']);
                        }

                        if (isset($conversion['size']['height'])) {
                            $imageConversion->height($conversion['size']['height']);
                        }

                        if (isset($conversion['fit']['width']) && isset($conversion['fit']['height'])) {
                            $imageConversion->fit('max', $conversion['fit']['width'], $conversion['fit']['height']);
                        }

                        if (isset($conversion['quality'])) {
                            $imageConversion->quality($conversion['quality']);
                            $imageConversion->optimize();
                        } else {
                            $imageConversion->quality($quality);
                        }

                        $imageConversion->performOnCollections($collection);
                    }
                }
            }
        }
    }
}
