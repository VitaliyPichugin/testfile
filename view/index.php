<?php include 'header.php' ?>
<body>
<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">TEST</a>
</nav>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <fieldset class="col-md-10">
                    <form>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Enter URL:</label>
                            <input type="text" class="form-control" name="url" id="url" placeholder="URL">
                        </div>
                        <button type="submit" name="checkUrl" id="checkUrl" class="btn btn-primary">Check</button>
                    </form>
                </fieldset>
            </div>
        </nav>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 ">
                <h1 class="h2">Report</h1>
                <div class="btn-toolbar mb-2 ml-0 mb-md-0">
                    <a id="downloadXls" href="http://<?=$_SERVER['SERVER_NAME']?>/report.xlsx" download class="btn-save btn btn-sm btn-outline-primary mr-2">
                        Download Table
                    </a>

                </div>
            </div>
            <div  class="preload col-md-12 "></div>
            <table class="table-report table table-stripped">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Название проверки</th>
                    <th colspan="2">Статус</th>
                    <th >Текущее состояние</th>
                </tr>
                </thead>
                <tbody >
                <?php if($_POST['url']): ?>
                    <?php foreach ($report as $k=>$v): ?>
                        <tr>
                            <th rowspan="2" style="vertical-align: sub" scope="row"><?=$k+1?></th>
                            <td rowspan="2"><?=$v['name_report']?></td>
                            <?php if($v['status_report'] == 'OK'): ?>
                            <td style="background-color: #4e8034" rowspan="2"><?=$v['status_report']?></td>
                            <?php else: ?>
                            <td style="background-color: #80533c" rowspan="2"><?=$v['status_report']?></td>
                            <?php endif; ?>
                            <td>Состояние</td>
                            <td><?=$v['condition']?></td>
                        </tr>
                        <tr>
                            <td>Рекомендации</td>
                            <td><?=$v['recommendations']?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                </tbody>
            </table>
        </main>
    </div>
</div>
</body>
<?php include 'footer.php' ?>
