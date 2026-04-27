<?php
/** @var yii\web\View $this */

/** @var string $content */
use app\assets\AppAsset;
use app\widgets\Alert;
use app\models\Part;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.png')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link rel="stylesheet" href="/css/galereya.css" />
        <link rel="stylesheet" href="/css/cloudzoom.css" />
        <script src="/js/hotkeys.js"></script>

        <link rel="stylesheet" href="/css/jqueryui.css">

    </head>
    <body class="d-flex flex-column h-100">
        <?php $this->beginBody() ?>

        <header id="header">
            <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->name,
                'brandUrl' => Yii::$app->homeUrl,
                'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
            ]);

            $items = [
                ['label' => 'Главная', 'url' => ['/site/index']],
                    //['label' => 'About', 'url' => ['/site/about']],
                    //['label' => 'Contact', 'url' => ['/site/contact']],
            ];

            if (!Yii::$app->user->isGuest) {
                $user = \app\models\User::findIdentity(Yii::$app->user->id);
                if ($user->role == 1) {
                    $items[] = ['label' => 'Кладбища',
                        'items' => [
                            ['label' => 'Кладбища', 'url' => '/web/cemetery'],
                            ['label' => 'Добавить', 'url' => '/web/cemetery/create'],
                            ['label' => 'Логи загрузки', 'url' => ['/book-upload']]
                        ],
                    ];

                    $items[] = ['label' => 'Книги',
                        'items' => [
                            ['label' => 'Книги', 'url' => '/web/book'],
                            ['label' => 'Добавить', 'url' => '/web/book/create'],
                        ],
                    ];

                    //$items[] = ['label' => 'Логи загрузки', 'url' => ['/book-upload']];


                    $items[] = ['label' => 'Пользователи',
                        'items' => [
                            ['label' => 'Пользователи', 'url' => '/web/user'],
                            ['label' => 'Добавить', 'url' => '/web/user/create'],
                        ],
                    ];

                    $items[] = ['label' => 'ЗАГС',
                        'items' => [
                            ['label' => 'ЗАГС', 'url' => '/web/zags'],
                            ['label' => 'Опечатки', 'url' => '/web/zags-raw'],
                        ],
                    ];

                    //$items[] = ['label' => 'Дебаг',
                    //    'url' => '/web/bad'
                    //];
                } else {

                    $items[] = ['label' => 'Книги',
                        'url' => '/web/book'
                    ];
                }
                if (($user->role != 1) && ($user->role != 4)) {
                    $items[] = ['label' => 'Поиск',
                        'url' => '/web/search'
                    ];
                } else {
                    $items[] = ['label' => 'Поиск',
                        'items' => [
                            ['label' => 'Поиск', 'url' => '/web/search'],
                            ['label' => 'Неточные данные', 'url' => '/web/record/vopros'],
                            ['label' => 'Удаленные данные', 'url' => '/web/record/deleted'],
                        ],
                    ];
                }
                if ($user->role == 22) {
                    $items[] = ['label' => 'Книги', 'url' => ['/operator/book']];
                    if ($user->book)
                        $items[] = ['label' => '  ' . $user->book->name, 'url' => ['/record?book=' . $user->book_id]];

                    $items[] = ['label' => 'Справка', 'url' => ['/operator/help']];
                }

                if (in_array($user->role, [1, 3])) {//($user->role == 3) {
                    $items[] = ['label' => 'Партии', 'url' => ['/part']];
                    $part = Part::find()
                            ->andWhere(['user_id' => $user->id])
                            ->andWhere(['status' => 1])
                            ->one();
                    if ($part) {
                        $items[] = ['label' => $part->cemetery->name . ', партия #' . $part->id, 'url' => ['/part/update-record?part_id=' . $part->id]];
                    }
                    /*
                      if ($user->book)
                      $items[] = ['label' => '  ' . $user->book->name, 'url' => ['/record?book=' . $user->book_id]];

                      $items[] = ['label' => 'Справка', 'url' => ['/operator/help']];
                     */
                }
            }

            $items[] = Yii::$app->user->isGuest ? ['label' => 'Войти', 'url' => ['/site/login']] : '<li class="nav-item">'
                    . Html::beginForm(['/site/logout'])
                    . Html::submitButton(
                            'Выйти (' . Yii::$app->user->identity->username . ')',
                            ['class' => 'nav-link btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>';

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => $items
            ]);
            NavBar::end();
            ?>
        </header>

        <main id="main" class="flex-shrink-0" role="main">
            <div class="container">
                <?php if (!empty($this->params['breadcrumbs'])): ?>
                    <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
                <?php endif ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </main>

        <footer id="footer" class="mt-auto py-3 bg-light">
            <div class="container">
                <div class="row text-muted">
                    <div class="col-md-6 text-center text-md-start">&copy; ИнфоКомм <?= date('Y') ?></div>
                    <div class="col-md-6 text-center text-md-end"><?php Yii::powered() ?></div>
                </div>
            </div>
        </footer>

        <?php $this->endBody() ?>
        <script src='/js/cloudzoom.js'></script>
        <script src="/js/jqueryui.js"></script>

    </body>
</html>
<?php $this->endPage() ?>
