<!-- TABLE -->
<table class="search-container">
    <tr>
        <td>
            <!-- PARAMETERS CONTAINER -->
            <div class="search-parameters-container">
                <div class="search-parameters-title">
                    Характеристики изображения:
                </div>
                <table>
                    <?php if (!empty($parameters['width']) && !empty($parameters['height'])): ?>
                    <!-- RESOLUTION -->
                    <tr>
                        <td>
                            <label for="width">Разрешение (px):</label>
                        </td>
                        <td>
                            <select name="width" id="width" class="input-1 search-parameters-width">
                                <option value="">-</option>
                                <?php foreach ($parameters['width'] as $value): ?>
                                <option value="<?php echo $value; ?>" <?php
                                    if ( isset($_POST['width']) && $_POST['width'] == $value ) echo ' selected="selected" '
                                    ?>><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="height" id="height" class="input-1 search-parameters-height">
                                <option value="">-</option>
                                <?php foreach ($parameters['height'] as $value): ?>
                                <option value="<?php echo $value; ?>" <?php
                                    if ( isset($_POST['height']) && $_POST['height'] == $value ) echo ' selected="selected" '
                                    ?>><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php if (!empty($parameters['extension']) ): ?>
                    <!-- EXTENSION -->
                    <tr>
                        <td>
                            <label for="extension">Формат:</label>
                        </td>
                        <td>
                            <select name="extension" id="extension" class="input-1">
                                <option value="">-</option>
                                <?php foreach ($parameters['extension'] as $value): ?>
                                <option value="<?php echo $value; ?>" <?php
                                    if ( isset($_POST['extension']) && $_POST['extension'] == $value ) echo ' selected="selected" '
                                    ?>><?php echo strtoupper($value); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php if (!empty($parameters['size']) ): ?>
                    <!-- FILE SIZE -->
                    <tr>
                        <td>
                            <label for="size">Размер файла (кбайт):</label>
                        </td>
                        <td>
                            <select name="size" id="size" class="input-1">
                                <option value="">-</option>
                                <?php foreach ($parameters['size'] as $value): ?>
                                <option value="<?php echo $value; ?>" <?php
                                    if ( isset($_POST['size']) && $_POST['size'] == $value ) echo ' selected="selected" '
                                    ?>><?php echo number_format( strtoupper($value) / 1024 , 1, '.', ' '); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php if (!empty($parameters['color_1']) ): ?>
                    <!-- COLOR 1 -->
                    <tr>
                        <td><label for="color_1">Преобладающий цвет 1:</label></td>
                        <td>
                            <div class="prefix-color">#</div><input name="color_1" id="color_1" class="input-1 colors-input" maxlength="6">
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php if ( false && !empty($parameters['color_2']) ): ?>
                    <!-- COLOR 2 -->
                    <tr>
                        <td><label for="color_2">Преобладающий цвет 2:</label></td>
                        <td>
                            <select name="color_2" id="color_2" class="input-1">
                                <option value="">-</option>
                                <?php foreach ($parameters['color_2'] as $value): ?>
                                <option value="<?php echo $value; ?>">#<?php echo strtoupper($value); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </td>
        <!-- SUBMIT -->
        <td>
            <input type="submit" name="search" value="ПОИСК" class="button-1 search-button">
        </td>
    </tr>
</table>