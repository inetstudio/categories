<?php

namespace InetStudio\Categories\Models;

use Cocur\Slugify\Slugify;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\Media;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use InetStudio\Meta\Models\Traits\Metable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Image\Exceptions\InvalidManipulation;
use Venturecraft\Revisionable\RevisionableTrait;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use InetStudio\Meta\Contracts\Models\Traits\MetableContract;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\SimpleCounters\Models\Traits\HasSimpleCountersTrait;

/**
 * InetStudio\Categories\Models\CategoryModel.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string|null $description
 * @property string|null $content
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $parent_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Kalnoy\Nestedset\Collection|\InetStudio\Categories\Models\CategoryModel[] $children
 * @property-read \Illuminate\Database\Eloquent\Collection|\InetStudio\SimpleCounters\Models\SimpleCounterModel[] $counters
 * @property-read \Illuminate\Contracts\Routing\UrlGenerator|string $href
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @property-read \Illuminate\Database\Eloquent\Collection|\Phoenix\EloquentMeta\Meta[] $meta
 * @property-read \InetStudio\Categories\Models\CategoryModel|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel d()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel findSimilarSlugs($attribute, $config, $slug)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Categories\Models\CategoryModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereLft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereRgt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Categories\Models\CategoryModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Categories\Models\CategoryModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Categories\Models\CategoryModel withoutTrashed()
 * @mixin \Eloquent
 */
class CategoryModel extends Model implements CategoryModelContract, MetableContract, HasMediaConversions
{
    use Metable;
    use Searchable;
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
        'name', 'slug', 'title', 'description', 'content',
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
     * Настройка полей для поиска.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $arr = array_only($this->toArray(), ['id', 'name', 'title', 'description', 'content']);

        return $arr;
    }

    /**
     * Возвращаем конфиг для генерации slug модели.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name',
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
     * Ссылка на объект.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getHrefAttribute()
    {
        return url(self::HREF.(! empty($this->slug) ? $this->slug : $this->id));
    }

    public function replicate(array $except = null)
    {
        $instance = $this->replicateNode($except);
        (new SlugService())->slug($instance, true);

        return $instance;
    }

    /**
     * Регистрируем преобразования изображений.
     *
     * @param Media|null $media
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null)
    {
        $quality = (config('categories.images.quality')) ? config('categories.images.quality') : 75;

        if (config('categories.images.conversions')) {
            foreach (config('categories.images.conversions') as $collection => $image) {
                foreach ($image as $crop) {
                    foreach ($crop as $conversion) {
                        $imageConversion = $this->addMediaConversion($conversion['name'])->nonQueued();

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
