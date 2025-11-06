# ShawGlobal Theme

Shaw Global 移民项目网站主题 - 基于 Hello Elementor 的子主题

## 目录结构

```
shawglobal-theme/
├── style.css              # 主题样式定义
├── functions.php          # 主题功能函数
├── assets/
│   ├── css/
│   │   └── custom.css     # 自定义样式
│   └── js/
│       └── custom.js      # 自定义脚本
├── templates/             # Elementor 模板 JSON 文件存放目录
├── README.md              # 本文件
└── ELEMENTOR_TEMPLATE_SAVE_GUIDE.md  # Elementor 模板保存机制详解
```

## Elementor Theme Builder 模板保存方式

### 方法一：通过 Elementor 后台导出/导入（推荐）

1. **导出模板**：
   - 在 Elementor 中设计好模板（Header、Footer、Single 等）
   - 进入 `Templates > Saved Templates`
   - 点击模板右侧的"导出"按钮
   - 保存 JSON 文件到 `templates/` 目录

2. **导入模板**：
   - 在 Elementor 后台 `Templates > Import Templates`
   - 选择 JSON 文件上传
   - 导入后模板会保存在数据库中

3. **应用模板**：
   - 进入 `Theme Builder`
   - 为相应的位置（Header、Footer、Single 等）选择已导入的模板

### 方法二：代码方式保存（高级）

Elementor 模板默认保存在数据库中（`wp_posts` 表，`post_type = 'elementor_library'`）。

如果需要通过代码方式导入，可以使用以下方法：

```php
// 在 functions.php 中添加自动导入功能
function auto_import_elementor_template($template_name, $json_file_path) {
    if (!class_exists('\Elementor\Plugin')) {
        return false;
    }

    $json_data = file_get_contents($json_file_path);
    $template_data = json_decode($json_data, true);

    // 使用 Elementor 导入器
    $import = new \Elementor\TemplateLibrary\Source_Local();
    // ... 导入逻辑
}
```

**注意**：推荐使用方法一，因为：
- Elementor 的导入/导出功能更稳定
- 可以保留所有元数据和设置
- 更容易维护和更新

### 模板文件命名建议

- `header-template.json` - 头部模板
- `footer-template.json` - 底部模板
- `single-immigration-project.json` - 项目详情页模板
- `archive-immigration-project.json` - 项目列表页模板
- `404-template.json` - 404 错误页模板

## 自定义 Widget 开发

如果需要创建自定义 Elementor Widget：

1. 在主题根目录创建 `includes/widgets/` 目录
2. 创建 Widget 类文件
3. 在 `functions.php` 中注册 Widget（见注释部分）

## 开发注意事项

- 所有自定义样式放在 `assets/css/custom.css`
- 所有自定义脚本放在 `assets/js/custom.js`
- Elementor 模板 JSON 文件放在 `templates/` 目录
- 保持与父主题的兼容性

## 参考文档

- [Elementor Theme Builder 文档](https://elementor.com/help/theme-builder/)
- [Hello Elementor 主题文档](https://elementor.com/hello-theme/)

