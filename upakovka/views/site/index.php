<?php
/** @var yii\web\View $this */
$this->title = 'Сведения о захоронениях г. Санкт-Петербурга';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Здравствуйте!</h1>

        <p class="lead">Контрольно-поисковая система.</p>

        <?php if (!Yii::$app->user->isGuest): ?>
            <p><a class="btn btn-lg btn-success" href="/web/cemetery">Кладбища</a></p>
        <?php else: ?>
            <p><a class="btn btn-lg btn-success" href="/web/site/login">Войти</a></p>
        <?php endif; ?>
    </div>

    <div class="body-content">


    </div>
</div>
