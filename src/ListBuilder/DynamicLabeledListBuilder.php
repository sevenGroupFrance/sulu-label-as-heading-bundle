<?php

namespace SevenGroupFrance\SuluLabelAsHeadline\ListBuilder;

use Sulu\Bundle\FormBundle\Entity\Dynamic;
use Sulu\Bundle\FormBundle\Entity\Form;
use Sulu\Bundle\FormBundle\ListBuilder\DynamicListBuilder;

class DynamicLabeledListBuilder extends DynamicListBuilder
{
    private array $formFieldsLabelCache = [];

    public function build(Dynamic $dynamic, string $locale): array
    {
        $entry = parent::build($dynamic, $locale);

        if (empty($entry)) {
            return $entry;
        }

        $labels = $this->getLabels($dynamic->getForm(), $locale);
        $entryLabeled = [];

        foreach ($entry[0] as $key => $value) {
            $entryLabeled[$labels[$key] ?? $key] = $value;
        }

        return [$entryLabeled];
    }

    private function getLabels(Form $form, string $locale): array
    {
        if (!isset($this->formFieldsLabelCache[$form->getId()])) {
            $labels = [];

            foreach ($form->getFields() as $field) {
                if (\in_array($field->getType(), Dynamic::$HIDDEN_TYPES)) {
                    continue;
                }

                $translation = $field->getTranslation($locale, false, true);

                if ($translation) {
                    $label = $translation->getShortTitle() ?: \strip_tags($translation->getTitle());
                    $labels[$field->getKey()] = $label;
                }
            }

            $this->formFieldsLabelCache[$form->getId()] = $labels;
        }

        return $this->formFieldsLabelCache[$form->getId()];
    }
}