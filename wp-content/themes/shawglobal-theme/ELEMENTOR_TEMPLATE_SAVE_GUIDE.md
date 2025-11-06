# Elementor Theme Builder 模板保存机制详解

## 📋 核心问题解答

### Q1: Elementor 模板保存在哪里？

**答案**：Elementor 模板默认保存在 **WordPress 数据库** 中，而不是文件系统中。

具体位置：
- **数据库表**：`wp_posts`
- **Post Type**：`elementor_library`
- **Meta 数据**：`wp_postmeta` 表中的 `_elementor_data` 字段（JSON 格式）

### Q2: 如何通过代码方式保存到文件？

虽然模板保存在数据库中，但可以通过以下方式将其保存为代码文件：

#### 方法一：手动导出（推荐）
1. 在 Elementor 后台设计模板
2. 进入 `Templates > Saved Templates`
3. 点击"导出"按钮，下载 JSON 文件
4. 将 JSON 文件放入子主题的 `templates/` 目录
5. 提交到 Git 仓库

#### 方法二：代码自动导出（开发时使用）
可以编写脚本定期导出模板到文件（见下文示例）

#### 方法三：通过 WP-CLI（命令行）
```bash
# 导出所有 Elementor 模板
wp elementor export --all --output=templates/
```

---

## 🔄 工作流程对比

### 传统主题开发流程
```
编辑 PHP 模板文件 → 保存到主题目录 → Git 提交 → 部署
```

### Elementor Theme Builder 流程
```
在后台设计模板 → 保存到数据库 → 导出 JSON → 放入代码库 → Git 提交 → 部署时导入
```

---

## 💾 模板保存的最佳实践

### 1. 设计阶段（在 WordPress 后台）

```php
// 不需要写代码！直接在 Elementor 后台操作：
// 1. Templates > Theme Builder > 创建 Header
// 2. 使用拖拽方式设计布局
// 3. 保存后模板自动存入数据库
```

### 2. 导出阶段（保存到代码）

#### 步骤 A：通过 Elementor 界面导出
1. 进入 `Elementor > Templates > Saved Templates`
2. 找到你创建的模板
3. 点击右侧的"导出"按钮
4. 保存 JSON 文件，命名为：`header-template.json`

#### 步骤 B：将文件放入子主题
```bash
# 移动文件到主题的 templates 目录
mv ~/Downloads/header-template.json \
   wp-content/themes/shawglobal-theme/templates/
```

### 3. 部署阶段（导入模板）

当部署到新环境时：

#### 选项 1：手动导入（简单）
1. 进入 `Elementor > Templates > Import Templates`
2. 上传 JSON 文件
3. 在 `Theme Builder` 中应用模板

#### 选项 2：自动导入（代码方式）

在 `functions.php` 中添加：

```php
/**
 * 自动导入 Elementor 模板
 */
function auto_import_elementor_templates() {
    // 只在首次激活时执行，避免重复导入
    if (get_option('elementor_templates_imported')) {
        return;
    }

    if (!class_exists('\Elementor\Plugin')) {
        return;
    }

    $templates_dir = HELLO_CHILD_THEME_PATH . '/templates/';
    $template_files = glob($templates_dir . '*.json');

    if (empty($template_files)) {
        return;
    }

    $source = new \Elementor\TemplateLibrary\Source_Local();

    foreach ($template_files as $file) {
        $file_data = file_get_contents($file);
        $imported = $source->import_template(
            basename($file, '.json'),
            $file_data
        );

        if ($imported && !is_wp_error($imported)) {
            // 导入成功后的处理
            error_log('Template imported: ' . basename($file));
        }
    }

    // 标记为已导入，避免重复执行
    update_option('elementor_templates_imported', true);
}
add_action('after_switch_theme', 'auto_import_elementor_templates');
```

---

## 🗂️ 文件结构说明

```
shawglobal-theme/
├── style.css                    # 主题样式定义（必需）
├── functions.php                # PHP 函数（必需）
├── assets/
│   ├── css/custom.css          # 自定义样式（代码）
│   └── js/custom.js            # 自定义脚本（代码）
└── templates/                   # Elementor 模板 JSON 文件（代码）
    ├── header-template.json    # 头部模板（从数据库导出）
    ├── footer-template.json    # 底部模板（从数据库导出）
    ├── single-project.json     # 详情页模板（从数据库导出）
    └── archive-project.json    # 列表页模板（从数据库导出）
```

**重要**：
- ✅ **代码文件**（`.css`, `.js`, `.php`）：直接编辑，保存到 Git
- ✅ **模板 JSON**：从 Elementor 导出后放入 `templates/`，也保存到 Git
- ❌ **数据库中的模板**：不直接进入代码库，通过导出 JSON 间接管理

---

## 🔧 子主题与 Elementor 的配合方式

### 1. 子主题的作用

子主题负责：
- ✅ 加载自定义 CSS/JS
- ✅ 注册自定义功能
- ✅ 存放导出的模板 JSON 文件
- ✅ 提供导入脚本（可选）

### 2. Elementor 的作用

Elementor 负责：
- ✅ 模板的**设计**和**编辑**（可视化界面）
- ✅ 模板的**存储**（数据库）
- ✅ 模板的**渲染**（前端输出）

### 3. 两者的配合流程

```
[子主题代码] ← 导出/导入 ← [Elementor 数据库]
     ↓                          ↓
  Git 仓库                   运行时渲染
```

---

## 📝 实际操作示例

### 场景：创建一个 Header 模板

#### 第 1 步：在 Elementor 后台设计
1. 登录 WordPress 后台
2. 进入 `Templates > Theme Builder > Add New`
3. 选择 "Header" 类型
4. 使用 Elementor 编辑器拖拽设计
5. 点击"发布"

**结果**：模板保存在数据库中，可以在前台看到效果

#### 第 2 步：导出为 JSON 文件
1. 进入 `Templates > Saved Templates`
2. 找到刚创建的 Header 模板
3. 点击"导出"按钮
4. 保存文件为 `header-template.json`

#### 第 3 步：放入代码库
```bash
# 复制到主题的 templates 目录
cp ~/Downloads/header-template.json \
   wp-content/themes/shawglobal-theme/templates/

# 添加到 Git
git add wp-content/themes/shawglobal-theme/templates/header-template.json
git commit -m "Add header template"
```

#### 第 4 步：部署时导入
在新环境部署后，模板会从 JSON 文件自动导入到数据库，然后在 Theme Builder 中应用。

---

## 🎯 关键要点总结

1. **Elementor 模板默认存数据库**，不是文件系统
2. **通过导出 JSON** 可以将模板保存为代码文件
3. **子主题存放导出的 JSON**，便于版本控制
4. **部署时导入 JSON** 到新环境的数据库
5. **子主题的 PHP/CSS/JS** 是代码，直接编辑；**模板 JSON** 是从数据库导出的，需要重新导入

---

## 🔗 相关资源

- [Elementor Theme Builder 官方文档](https://elementor.com/help/theme-builder/)
- [Elementor 模板导入/导出](https://elementor.com/help/how-to-export-or-import-templates/)
- [WordPress 子主题开发](https://developer.wordpress.org/themes/advanced-topics/child-themes/)

