<?php

/**
 * @OA\Schema(
 *     description="Карточка на самолет",
 *     type="object",
 *     required={"type", "departure", "arrival", "flight_number"},
 * )
 */
class FlightCard
{
    /**
     * @OA\Property(property="type", description="Вид транспорта", type="string", example="flight")
     * @var string
     */
    public $type;

    /**
     * @OA\Property(property="departure", description="Точка отправления", type="string", example="Gerona Airport")
     * @var string
     */
    public $departure;

    /**
     * @OA\Property(property="arrival", description="Точка прибытия", type="string", example="Stockholm")
     * @var string
     */
    public $arrival;

    /**
     * @OA\Property(property="flight_number", description="Номер рейса", type="string", example="SK455")
     * @var string
     */
    public $flight_number;

    /**
     * @OA\Property(property="gate", description="Номер выхода", type="string", example="45B")
     * @var string
     */
    public $gate;

    /**
     * @OA\Property(property="seat", description="Номер места", type="string", example="3A")
     * @var string
     */
    public $seat;

    /**
     * @OA\Property(property="baggage_drop", description="Сдача багажа", type="string", example="ticket counter 344")
     * @var string
     */
    public $baggage_drop;

    /**
     * @OA\Property(property="baggage_transfer", description="Перенос багажа", type="boolean", example=true)
     * @var bool
     */
    public $baggage_transfer;

    public function assignProperties($card)
    {
        $this->type = $card['type'];
        $this->departure = $card['departure'];
        $this->arrival = $card['arrival'];
        $this->flight_number = $card['flight_number'];
        if (isset($card['gate'])) $this->gate = $card['gate'];
        if (isset($card['seat'])) $this->seat = $card['seat'];
        if (isset($card['baggage_drop'])) $this->baggage_drop = $card['baggage_drop'];
        if (isset($card['baggage_transfer'])) $this->baggage_transfer = $card['baggage_transfer'];
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
        if (empty($card['flight_number'])) {
            $cardError = "Missing 'flight_number' field";
        }
        return $cardError;
    }

    // Генерация текстового описания
    public function generateDescription()
    {
        $description = "From {$this->departure}, take flight {$this->flight_number} to {$this->arrival}. Gate {$this->gate}. Seat {$this->seat}.";
        if ($this->baggage_drop) {
            $description .= " Baggage drop at {$this->baggage_drop}. ";
        } elseif ($this->baggage_transfer) {
            $description .= " Baggage will be automatically transferred from your last leg. ";
        } else {
            $description .= " ";
        }
        return $description;
    }
}
