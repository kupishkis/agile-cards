<?php
/**
 * Cards.php
 *
 * @filesource
 * @created 14-04-18
 */

namespace App\Service;

/**
 * Cards service.
 *
 * @package AgileCards\Service
 */
class Cards
{
    /**
     * Parses file and returns found story cards in it.
     *
     * @param string $filepath
     * @return false|array
     */
    public function parseFile($filepath)
    {
        $xml   = simplexml_load_file($filepath);

        if (!$xml instanceof \SimpleXMLElement) {
            return false;
        }

        $cards = [];

        foreach ($xml->channel->item as $card) {
            switch ($card->type['id']) {
                case 1:
                case 9:
                    $icon = 'bug';
                    break;
                case 19:
                    $icon = 'folder';
                    break;
                default:
                    $icon = 'wrench';
            }

            $id = (string) $card->key;
            $cards[$id] = [
                'id'          => $id,
                'parent'      => (string) $card->parent,
                'key'         => (string) $card->key,
                'type'        => (string) $card->type,
                'summary'     => (string) $card->summary,
                'description' => (string) $card->description,
                'priority'    => (string) $card->priority,
                'status'      => (string) $card->status,
                'resolution'  => (string) $card->resolution,
                'assignee'    => (string) $card->assignee,
                'reporter'    => (string) $card->reporter,
                'estimate'    => (string) $card->timeestimate,
                'timespent'   => (string) $card->aggregatetimespent,
                'icon'        => $icon,

            ];
        }

        return $cards;
    }
}
