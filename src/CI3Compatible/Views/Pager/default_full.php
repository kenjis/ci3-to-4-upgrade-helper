<?php

/**
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */

$surroundCount = 2;
$pager->setSurroundCount($surroundCount);
?>

<?php if ($pager->hasPreviousPage()) : ?>
    <?php if ($pager->getPreviousPageNumber() > $surroundCount) : ?>
    <a href="<?= $pager->getFirst() ?>" data-ci-pagination-page="<?= $pager->getFirstPageNumber() ?> rel="start">&lsaquo; <?= lang('Pager.first') ?></a>
    <?php endif ?>

    <a href="<?= $pager->getPreviousPage() ?>" data-ci-pagination-page="<?= $pager->getPreviousPageNumber() ?>" rel="prev">&lt;</a>
<?php endif ?>

<?php foreach ($pager->links() as $link) : ?>
    <?php if ($link['active']) : ?>
        <strong><?= $link['title'] ?></strong>
    <?php else : ?>
        <a href="<?= $link['uri'] ?>" data-ci-pagination-page="<?= $link['title'] ?>">
            <?= $link['title'] ?>
        </a>
    <?php endif ?>
<?php endforeach ?>

<?php if ($pager->hasNextPage()) : ?>
    <a href="<?= $pager->getNextPage() ?>" data-ci-pagination-page="<?= $pager->getNextPageNumber() ?>" rel="next">&gt;</a>

    <?php if ($pager->getCurrentPageNumber() < $pager->getPageCount() - $surroundCount) : ?>
    <a href="<?= $pager->getLast() ?>" data-ci-pagination-page="<?= $pager->getLastPageNumber() ?>"><?= lang('Pager.last') ?> &rsaquo;</a>
    <?php endif ?>
<?php endif ?>
