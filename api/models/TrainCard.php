<?php

/**
 * @OA\Schema(
 *     description="Карточка на поезд",
 *     type="object",
 *     required={"type", "departure", "arrival", "train_number"},
 * )
 */
class TrainCard
{
    /**
     * @OA\Property(property="type", type="string", example="train")
     * @var string
     */
    public $type;

    /**
     * @OA\Property(property="departure", type="string", example="Madrid")
     * @var string
     */
    public $departure;

    /**
     * @OA\Property(property="arrival", type="string", example="Barcelona")
     * @var string
     */
    public $arrival;

    /**
     * @OA\Property(property="train_number", type="string", example="78A")
     * @var string
     */
    public $train_number;

    /**
     * @OA\Property(property="seat", type="string", example="45B")
     * @var string
     */
    public $seat;

    public function assignProperties($card)
    {
        $this->type = $card['type'];
        $this->departure = $card['departure'];
        $this->arrival = $card['arrival'];
        $this->train_number = $card['train_number'];
        if (isset($card['seat'])) $this->seat = $card['seat'];
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
        if (empty($card['train_number'])) {
            $cardError = "Missing 'train_number' field";
        }
        return $cardError;
    }

    // Генерация текстового описания
    public function generateDescription()
    {
        return "Take train {$this->train_number} from {$this->departure} to {$this->arrival}. Seat {$this->seat}. ";
    }
}
