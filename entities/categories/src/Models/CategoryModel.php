<?php

namespace InetStudio\CategoriesPackage\Categories\Models;

use Cocur\Slugify\Slugify;
use OwenIt\Auditing\Auditable;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\Uploads\Models\Traits\HasImages;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use InetStudio\MetaPackage\Meta\Models\Traits\HasMeta;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use InetStudio\AdminPanel\Models\Traits\HasDynamicRelationships;
use InetStudio\AdminPanel\Base\Models\Traits\Scopes\BuildQueryScopeTrait;
use InetStudio\SimpleCounters\Counters\Models\Traits\HasSimpleCountersTrait;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;

/**
 * Class CategoryModel.
 */
class CategoryModel extends Model implements CategoryModelContract
{
    use HasMeta;
    use HasImages;
    use Auditable;
    use SoftDeletes;
    use Sluggable, NodeTrait {
        NodeTrait::replicate as replicateNode;
        Sluggable::replicate as replicateSlug;
    }
    use SluggableScopeHelpers;
    use BuildQueryScopeTrait;
    use HasSimpleCountersTrait;
    use HasDynamicRelationships;

    /**
     * Тип сущности.
     */
    const ENTITY_TYPE = 'category';

    /**
     * Часть слага для сущности.
     */
    const HREF = '/category/';

    /**
     * Конфиг для модели.
     *
     * @var string
     */
    protected $config = 'categories';

    /**
     * Should the timestamps be audited?
     *
     * @var bool
     */
    protected $auditTimestamps = true;

    /**
     * Настройки для генерации изображений.
     *
     * @var array
     */
    protected $images = [
        'config' => 'categories',
        'model' => 'category',
    ];

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
        'name',
        'slug',
        'title',
        'description',
        'content',
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
     * Возвращаем конфиг для генерации slug модели.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'unique' => true,
            ],
        ];
    }

    /**
     * Загрузка модели.
     */
    protected static function boot()
    {
        parent::boot();

        self::$buildQueryScopeDefaults['columns'] = [
            'id',
            'slug',
            'name',
        ];

        self::$buildQueryScopeDefaults['relations'] = [
            'meta' => function (MorphMany $metaQuery) {
                $metaQuery->select(['metable_id', 'metable_type', 'key', 'value']);
            },

            'media' => function (MorphMany $mediaQuery) {
                $mediaQuery->select(
                    [
                        'id',
                        'model_id',
                        'model_type',
                        'collection_name',
                        'file_name',
                        'disk',
                        'conversions_disk',
                        'uuid',
                        'mime_type',
                        'custom_properties',
                        'responsive_images',
                    ]
                );
            },
        ];
    }

    /**
     * Сеттер атрибута name.
     *
     * @param $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strip_tags($value);
    }

    /**
     * Сеттер атрибута title.
     *
     * @param $value
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = strip_tags($value);
    }

    /**
     * Сеттер атрибута slug.
     *
     * @param $value
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strip_tags($value);
    }

    /**
     * Сеттер атрибута description.
     *
     * @param $value
     */
    public function setDescriptionAttribute($value)
    {
        $value = (isset($value['text'])) ? $value['text'] : (! is_array($value) ? $value : '');

        $this->attributes['description'] = trim(str_replace('&nbsp;', ' ', strip_tags($value)));
    }

    /**
     * Сеттер атрибута content.
     *
     * @param $value
     */
    public function setContentAttribute($value)
    {
        $value = (isset($value['text'])) ? $value['text'] : (! is_array($value) ? $value : '');

        $this->attributes['content'] = trim(str_replace('&nbsp;', ' ', $value));
    }

    /**
     * Геттер атрибута type.
     *
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return self::ENTITY_TYPE;
    }

    /**
     * Геттер атрибута href.
     *
     * @return string
     */
    public function getHrefAttribute(): string
    {
        return url(self::HREF.($this->getAttribute('slug') ?: $this->getAttribute('id')));
    }

    /**
     * Разрешение конфликта трейтов Sluggable, NodeTrait.
     *
     * @param  array|null  $except
     *
     * @return Model
     */
    public function replicate(array $except = null)
    {
        $instance = $this->replicateNode($except);
        (new SlugService())->slug($instance, true);

        return $instance;
    }

    public function customizeSlugEngine(Slugify $engine)
    {
        $rules = [
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'jo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shh',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'je',
            'ю' => 'ju',
            'я' => 'ja',
        ];

        $engine->addRules($rules);

        return $engine;
    }
}
