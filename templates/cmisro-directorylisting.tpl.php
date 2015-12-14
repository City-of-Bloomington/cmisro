<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $basedir
 * @param array $subdir      The currently chosen subdirectry
 * @param array $directories The subdirectories of $basedir to be used for navigation
 * @param array $documents   The documents to be listed in the current view
 * @param stdClass $node     The drupal node object
 */
?>
<section class="cmisro container">
    <h1><?= $basedir['title']; ?></h1>
    <nav>
    <?php
        $uri = _cmisro_folder_uri($node->nid, $basedir['id']);

        foreach ($directories as $dir) {
            $attr = $dir['id'] === $subdir['id']
                ? ['attributes' => ['class' => ['current']]]
                : [];
            echo l($dir['title'], $uri."/$dir[id]", $attr);
        }
    ?>
    </nav>
    <div class="listing">
        <dl><dd><dl>
                <?php
                    foreach ($documents as $o) {
                        $a = theme('cmisro_item', ['object'=>$o]);
                        echo "<dd>$a</dd>";
                    }
                ?>
                </dl>
            </dd>
        </dl>
    </div>
</section>