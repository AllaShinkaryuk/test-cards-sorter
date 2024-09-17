<?php

/**
 * @OA\Schema(
 *     description="Карточка на автобус",
 *     type="object",
 *     required={"type", "departure", "arrival", "bus_number"},
 * )
 */
class BusCard
{
    /**
     * @OA\Property(property="type", type="string", example="bus")
     * @var string
     */
    public $type;

    /**
     * @OA\Property(property="departure", type="string", example="Barcelona")
     * @var string
     */
    public $departure;

    /**
     * @OA\Property(property="arrival", type="string", example="Gerona Airport")
     * @var string
     */
    public $arrival;

    /**
     * @OA\Property(property="bus_number", type="string", example="Airport bus")
     * @var string
     */
    public $bus_number;

    public function assignProperties($card)
    {
        $this->type = $card['type'];
        $this->departure = $card['departure'];
        $this->arrival = $card['arrival'];
        $this->bus_number = $card['bus_number'];
    }

    // Поиск возможных ошибок в атрибутах карточки
    public function getErrors($card)
    {
        $cardError = false;
        if (empty($card['departure'])) {
            $cardError = "Missing 'departure' field";
        }
        if (empty($card['arrival'])) {
            $cardError = "Missing 'arrival' field";
        }
        if (empty($card['bus_number'])) {
            $cardError = "Missing 'bus_number' field";
        }
        return $cardError;
    }

    // Генерация текстового описания
    public function generateDescription()
    {
        return "Take the bus {$this->bus_number} from {$this->departure} to {$this->arrival}. No seat assignment.\n";
    }
}
