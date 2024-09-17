<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Сортировщик карточек путешественника",
 *     description="PHP API, который сортирует список карточек и возвращает словесное описание, как проделать путешествие."
 * )
 */

/**
 * @OA\POST(
 *     path="/api/sort",
 *     summary="Сортировка посадочных карточек",
 *     tags={"cards"},
 *     @OA\RequestBody(
 *        required = true,
 *        description = "Массив из посадочных карточек на различные виды транспорта, которые доставляют из точки A в точку B. Карточки перемешаны в случайном порядке.",
 *        @OA\JsonContent(
 *           type="array",
 *           @OA\Items(oneOf={
 *              @OA\Schema(ref="#/components/schemas/BusCard"),
 *				@OA\Schema(ref="#/components/schemas/FlightCard"),
 *				@OA\Schema(ref="#/components/schemas/TrainCard"),
 *           })
 *        ),
 *     ),
 *     @OA\Response(
 *        response="200",
 *        description="Запрос успешно выполнен",
 *        @OA\JsonContent(
 *           type="object",
 *           @OA\Property(property="description", type="string", example="Take train 78A from Madrid to Barcelona. Seat 45B.")
 *        ),
 *     ),
 *     @OA\Response(
 *        response="400",
 *        description="Некорректные входные данные",
 *        @OA\JsonContent(
 *           type="object",
 *           @OA\Property(property="error", type="string", example="Boarding cards do not form a continuous chain.")
 *        ),
 *     ),
 * )
 */

/*
    Пример входных данных 
    (полный список атрибутов можно посмотреть в соответствующих классах для каждого вида транспорта):
	[
	  {
		"type": "bus",
		"departure": "Barcelona",
		"arrival": "Gerona Airport",
		"bus_number": "65",
	  },
	  {
		"type": "flight",
		"departure": "Gerona Airport",
		"arrival": "Stockholm",
		"flight_number": "SK455",
	  },
	  {
		"type": "train",
		"departure": "Madrid",
		"arrival": "Barcelona",
		"train_number": "78A",
	  }
	]
 */

// Классы для различных видов транспорта
require_once 'models/BusCard.php';
require_once 'models/FlightCard.php';
require_once 'models/TrainCard.php';

// Функция, валидидирующая и сортирующая карточки
function sortAndValidateBoardingCards($boardingCards)
{
    foreach ($boardingCards as $key => &$card) {

        $cardNumber = $key + 1;

        // Выбор класса в зависимости от типа транспорта
        switch ($card['type']) {
            case 'bus':
                $cardObj = new BusCard();
                break;
            case 'flight':
                $cardObj = new FlightCard();
                break;
            case 'train':
                $cardObj = new TrainCard();
                break;
            default:
                return ['status' => false, 'error' => "Card #{$cardNumber}. Unknown type '{$card['type']}'"];
        }

        $cardError = $cardObj->getErrors($card);
        if (empty($cardError)) {
            $cardObj->assignProperties($card);
            $card['cardDescription'] = $cardObj->generateDescription();
        } else return ['status' => false, 'error' => "Card #{$cardNumber}. " . $cardError];
    }

    // Сортировка посадочных карточек и генерация текстового описания
    try {
        $sortedCards = sortBoardingCards($boardingCards);

        $tripDescription = '';
        foreach ($sortedCards as $card) {
            $tripDescription .= $card['cardDescription'] . "<br>";
        }

        return ['status' => true, 'description' => $tripDescription];
    } catch (Exception $e) {
        return ['status' => false, 'error' => $e->getMessage()];
    }
}

// Алгоритм сортировки карточек
function sortBoardingCards($boardingCards)
{
    // Создаем ассоциативный массив для хранения карточек по стартовым точкам
    $routeMap = [];

    // Создаем массив финишных точек
    $finishPoints = [];

    // Заполняем ассоциативный массив и собираем финишные точки в массив
    foreach ($boardingCards as $card) {
        $routeMap[$card['departure']] = $card;
        $finishPoints[] = $card['arrival'];
    }

    // Проверяем, нет ли дубликатов среди стартовых и финишных точек
    if (sizeof($routeMap) < sizeof($boardingCards)) {
        throw new Exception("There are identical starting points in this list.");
    }
    if (sizeof(array_unique($finishPoints)) < sizeof($boardingCards)) {
        throw new Exception("There are identical finishing points in this list.");
    }

    // Определяем начальную точку (которая не встречается как финишная)
    $startPoint = null;

    foreach ($routeMap as $departure => $card) {
        if (!in_array($departure, $finishPoints)) {
            $startPoint = $departure;
            break;
        }
    }

    if ($startPoint === null) throw new Exception("The starting point cannot be located.");

    // Собираем маршрут
    $sortedRoute = [];
    $currentPoint = $startPoint;

    while ($currentPoint !== null && isset($routeMap[$currentPoint])) {
        $sortedRoute[] = $routeMap[$currentPoint];
        $currentPoint = $routeMap[$currentPoint]['arrival'];
    }

    if (sizeof($sortedRoute) != sizeof($boardingCards)) {
        throw new Exception("Boarding cards do not form a continuous chain.");
    }

    return $sortedRoute;
}

// Обработка входящего запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($boardingCards)) $boardingCards = json_decode(file_get_contents('php://input'), true);

    if (empty($boardingCards)) {
        http_response_code(400);
        echo json_encode(['error' => 'No boarding cards provided']);
        exit;
    }

    $result = sortAndValidateBoardingCards($boardingCards);

    if ($result['status']) {
        http_response_code(200);
        echo json_encode(['description' => $result['description']]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => $result['error']]);
    }

    exit;
}

// Обработка запроса в случае неподдерживаемого HTTP-метода
http_response_code(405);
echo json_encode(['error' => 'Method is not allowed']);
