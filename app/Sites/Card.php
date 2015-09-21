<?php namespace DemocracyApps\GB\Sites;
/**
 *
 * This file is part of the Government Budget Explorer (GBE).
 *
 *  The GBE is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GBE is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the GBE.  If not, see <http://www.gnu.org/licenses/>.
 */

use DemocracyApps\GB\Utility\EloquentPropertiedObject;

class Card extends EloquentPropertiedObject
{
    protected $table = 'cards';

    public function asSimpleObject($props = null)
    {
        $card = new \stdClass();

        $card->id = $this->id;
        $card->cardSet = $this->card_set;
        $card->title = $this->title;
        $card->body = $this->body;
        $card->image = $this->image;
        $card->link = $this->link;
        if ($props != null) {
            foreach ($props as $key => $value) {
                $card->{$key} = $value;
            }
        }
        return $card;
    }
}
