<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var int $taskId
 */
$this->title = 'Состояние фоновой задачи';
?>

<div class="d-flex flex-column p-3" style="height: calc(100vh - 110px - 40px)">
    <div class="task-status-box" style="margin-top: 30px;">
        <h2>Прогресс выполнения задачи</h2>
        
        <div id="status-message" class="alert alert-info">
            Инициализация...
        </div>

        <div id="status-progress" class="progress">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                role="progressbar" style="width: 0%"></div>
        </div>
        <div id="progress-status" class="d-flex justify-content-between mt-2 text-muted small">
            Статус
        </div>
    </div>

    <div class="mt-4 flex-grow-1 d-flex flex-column min-height-0">
        <label class="form-label fw-bold">Логи системы:</label>
        
        <pre id="logs-text" class="bg-dark text-light p-3 rounded flex-grow-1 m-0" style="overflow-y: auto; height: 0; "></pre>
    </div>
</div>

<?php
// Пишем простой JS, который будет опрашивать наш контроллер
// URL теперь указывает на наш SSE-экшен (например, check-status-sse)
$checkUrl = Url::to(['check-status', 'id' => $taskId]);

$js = <<<JS
// Создаем постоянное соединение с сервером
const eventSource = new EventSource('{$checkUrl}');

eventSource.onmessage = function(event) {
    // Сервер присылает JSON-строку, декодируем её
    const data = JSON.parse(event.data);
    
    // Обновляем базовое сообщение о статусе
    $('#status-message').text(data.message);

    if (data.status === 'done') {
        // Если задача успешно завершена — закрываем SSE-соединение
        eventSource.close();

        $('#status-progress').hide();
        $('#progress-status').addClass('d-none').removeClass('d-flex');

        $('#status-message').removeClass('alert-info').addClass('alert-success');
        $('#status-message').text(data.message);
    } 
    else if (data.status === 'unknown') {
        // Если произошла ошибка — закрываем SSE-соединение
        eventSource.close();

        $('#status-progress').hide();
        $('#progress-status').addClass('d-none').removeClass('d-flex');
        $('#status-message').removeClass('alert-info').addClass('alert-danger');

        if (data.logs) {
            var pre = $('#logs-text');
            pre.text(data.logs);
            pre.scrollTop(pre[0].scrollHeight);
        }
    } 
    else {
        // Задача в процессе выполнения (waiting или running)
        $('#progress-bar').css('width', data.percentage + '%');
        $('#progress-status').text(data.name);

        if (data.logs) {
            var pre = $('#logs-text');
            pre.text(data.logs);
            pre.scrollTop(pre[0].scrollHeight);
        }
    }
};

// На случай непредвиденных ошибок сети (сервер упал, таймаут прокси)
eventSource.onerror = function(err) {
    console.error("SSE connection error:", err);
    // Браузер автоматически попытается переподключиться к серверу самостоятельно.
    // Если вам нужно остановить попытки при ошибке связи, раскомментируйте строку ниже:
    // eventSource.close();
};
JS;

$this->registerJs($js);
?>