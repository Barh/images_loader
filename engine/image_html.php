<!-- IMAGE PARAMETERS -->
<div>
    <table>
        <tr>
            <td>
                <!-- IMAGE -->
                <div class="image-container">
                    <a href="/images/<?php echo $data['id'].'.'.$data['extension']; ?>" target="_blank">
                        <img src="/images/<?php echo $data['id'].'.'.$data['extension']; ?>" alt="Текстура (Параметры)" class="image-plate" title="Открыть в новой вкладке">
                    </a>
                </div>
            </td>
            <td>
                <!-- PARAMETERS -->
                <table class="image-parameters">
                    <?php if (!empty($data['width']) && !empty($data['height'])): ?>
                    <!-- RESOLUTION -->
                    <tr>
                        <td class="image-label">Разрешение (px):</td>
                        <td class="image-data"><?php echo $data['width'].'x'.$data['height']; ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($data['extension']) ): ?>
                    <!-- EXTENSION -->
                    <tr>
                        <td class="image-label">Формат:</td>
                        <td class="image-data"><?php echo strtoupper($data['extension']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($data['size']) ): ?>
                    <!-- FILE SIZE -->
                    <tr>
                        <td class="image-label">Размер (кбайт):</td>
                        <td class="image-data"><?php echo number_format( strtoupper($data['size']) / 1024 , 1, '.', ' '); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($data['color_1']) ): ?>
                    <!-- COLOR 1 -->
                    <tr>
                        <td class="image-label">Преобладающий цвет 1:</td>
                        <td class="image-data"><div class="inline-block" style="display: inline-block; width: 10px; height: 10px; border: 1px solid #000; background-color: #<?php echo $data['color_1']; ?>; margin-right: 4px;"></div>#<?php echo strtoupper($data['color_1']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($data['color_2']) ): ?>
                    <!-- COLOR 2 -->
                    <tr>
                        <td class="image-label">Преобладающий цвет 2:</td>
                        <td class="image-data"><div class="inline-block" style="display: inline-block; width: 10px; height: 10px; border: 1px solid #000; background-color: #<?php echo $data['color_2']; ?>; margin-right: 4px;"></div>#<?php echo strtoupper($data['color_2']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </td>
        </tr>
    </table>
</div>