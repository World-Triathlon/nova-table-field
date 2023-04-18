<?php

namespace OptimistDigital\NovaTableField;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;

class Table extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-table-field';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

    /**
     * Determine if new rows are able to be added.
     *
     * @var bool
     */
    public $canAddRows = true;

    /**
     * Determine if rows are able to be deleted.
     *
     * @var bool
     */
    public $canDeleteRows = true;

    /**
     * Determine if new columns are able to be added.
     *
     * @var bool
     */
    public $canAddColumns = true;


    /**
     * Determine if columns are able to be deleted.
     *
     * @var bool
     */
    public $canDeleteColumns = true;


    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);
        $this->fillCallback = static function(NovaRequest $request, $model, $attribute, $requestAttribute) {

            if (! $request->exists($requestAttribute)) {
                return;
            }

            if(Str::contains($requestAttribute, '->')) {
                $paths = explode('->', $requestAttribute);

                $root = array_shift($paths);
                $value = $model->{$root};

                data_set($value, $paths, json_decode($request[$requestAttribute], true));

                return $model->setAttribute($root, $value);
            }

            $model->{$attribute} = json_decode($request[$requestAttribute], true);
        };

        $this->resolveCallback = static function($request, $model, $attribute) {
            return $request;
        };


    }

    /**
     * The minimum number of rows in the table.
     *
     * @param mixed $min
     * @return $this
     */
    public function minRows($min)
    {
        return $this->withMeta(['minRows' => $min]);
    }

    /**
     * The maximum number of rows in the table.
     *
     * @param mixed $max
     * @return $this
     */
    public function maxRows($max)
    {
        return $this->withMeta(['maxRows' => $max]);
    }

    /**
     * The minimum number of columns in the table.
     *
     * @param mixed $min
     * @return $this
     */
    public function minColumns($min)
    {
        return $this->withMeta(['minColumns' => $min]);
    }

    /**
     * The maximum number of columns in the table.
     *
     * @param mixed $max
     * @return $this
     */
    public function maxColumns($max)
    {
        return $this->withMeta(['maxColumns' => $max]);
    }

    public function headings(array $headings, bool $assoc = true)
    {
        return $this->withMeta([
            'headings' => $headings,
            'assoc' => $assoc
        ]);
    }

    /**
     * Disable adding new rows and columns.
     *
     * @return $this
     */
    public function disableAddingRows()
    {
        $this->canAddRows = false;

        return $this;
    }

    /**
     * Disable deleting rows and columns.
     *
     * @return $this
     */
    public function disableDeletingRows()
    {
        $this->canDeleteRows = false;

        return $this;
    }

    /**
     * Disable adding new rows and columns.
     *
     * @return $this
     */
    public function disableAddingColumns()
    {
        $this->canAddColumns = false;

        return $this;
    }

    /**
     * Disable deleting rows and columns.
     *
     * @return $this
     */
    public function disableDeletingColumns()
    {
        $this->canDeleteColumns = false;

        return $this;
    }

    /** @deprecated  */
    public function disableAdding()
    {
        $this->disableAddingRows();
        return $this->disableAddingColumns();
    }

    /** @deprecated  */
    public function disableDeleting()
    {
        $this->disableDeletingRows();
        return $this->disableDeletingColumns();
    }

    /**
     * Prepare the field element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'canAddRows' => $this->canAddRows,
            'canDeleteRows' => $this->canDeleteRows,
            'canAddColumns' => $this->canAddColumns,
            'canDeleteColumns' => $this->canDeleteColumns,
        ]);
    }
}
