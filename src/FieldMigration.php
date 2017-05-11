<?php

namespace StoutLogic\ACF\Migrations;

class FieldMigration
{
    /**
     * @var string Field
     */
    private $field;

    /**
     * @var Transform[]
     */
    private $transforms = [];

    /**
     * Include the option and options pages by default, this is what
     * ACF/ACF PRO will name an unnamed options page.
     * @var array
     */
    private $optionPages = ['option', 'options'];

    /**
     * MigrateField constructor.
     * @param string $fieldKey ACF Field key of field to be migrated
     */
    public function __construct($fieldKey)
    {
        $this->field = new Field($fieldKey);
    }


    /**
     * @param Transform $transform
     * @return $this
     */
    public function addTransform(Transform $transform)
    {
        $this->transforms[] = $transform;

        return $this;
    }

    /**
     * @return array
     */
    public function getTransforms()
    {
        return $this->transforms;
    }

    /**
     * Change the field name.
     * @param stirng $newName
     * @return $this
     */
    public function changeName($newName)
    {
        $this->transforms[] = new ChangeNameTransform($newName);

        return $this;
    }

    /**
     * Change the field key.
     * @param stirng $newKey
     * @return $this
     */
    public function changeKey($newKey)
    {
        $this->transforms[] = new ChangeKeyTransform($newKey);

        return $this;
    }

    /**
     * Pefrom the migration.
     */
    public function migrate()
    {
        array_map([$this, 'applyTransform'], $this->transforms);
    }


    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }


    public function includeOptionPage($optionPage)
    {
        $this->optionPages[] = $optionPage;

        return $this;
    }

    public function excludeOptionPage($optionPage)
    {
        $this->optionPages = array_merge(array_filter($this->optionPages, function ($o) use ($optionPage) {
            return $o !== $optionPage;
        }));

        return $this;
    }

    public function getSubjects()
    {
        return array_merge(
            $this->getPosts(),
            $this->getTerms(),
            $this->getUsers(),
            $this->getComments(),
            $this->getOptions()
        );
    }

    private function applyTransform($transform)
    {
        $transform->setField($this->getField());

        foreach ($this->getSubjects() as $subject) {
            $transform->transformSubject($subject);
        }

        $transform->transformField();
    }

    /**
     * @return array
     */
    protected function getPosts()
    {
        return array_map(
            function ($id) {
                return new Post($id);
            },
            get_posts([
                'fields' => 'ids',
                'meta_value' => $this->getField()->getKey(),
            ])
        );
    }

    /**
     * @return array
     */
    protected function getTerms()
    {
        return array_map(
            function ($id) {
                return new Term($id);
            },
            get_terms([
                'fields' => 'ids',
                'hide_empty' => false,
                'meta_value' => $this->getField()->getKey()
            ])
        );
    }

    /**
     * @return array
     */
    protected function getUsers()
    {
        return array_map(
            function ($id) {
                return new User($id);
            },
            get_users([
                'fields' => 'ids',
                'hide_empty' => false,
                'meta_value' => $this->getField()->getKey()
            ])
        );
    }

    /**
     * @return array
     */
    protected function getComments()
    {
        return array_map(
            function ($id) {
                return new Comment($id);
            },
            get_comments([
                'fields' => 'ids',
                'hide_empty' => false,
                'meta_value' => $this->getField()->getKey()
            ])
        );
    }
    /**
     * @return array
     */
    protected function getOptions()
    {
        return array_filter(
            array_map(function ($id) {
                return new OptionPage($id);
            }, $this->optionPages),
            function (OptionPage $optionPage) {
                return $optionPage->hasMetaValue($this->getField()->getKey());
            }
        );
    }

    /**
     * @return array
     */
    public
    function getOptionPages()
    {
        return $this->optionPages;
    }
}
