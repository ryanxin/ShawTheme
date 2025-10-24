<!-- 07dfd8fd-b8c3-49cc-9247-f3fc95b1b053 21545be3-40a0-4472-8ab0-746bff21ee4a -->
# WordPress 移民项目主题开发方案

## 一、技术架构

### 主题部分（FSE Block Theme）

- **基础框架**：基于 WordPress 6.4+ FSE 特性，使用 `theme.json` 配置设计系统
- **构建工具**：`@wordpress/scripts` + React（支持 JSX 语法）
- **核心文件**：
  - `theme.json`：定义颜色、字体、间距等设计 tokens
  - `functions.php`：注册块、样式、脚本
  - `templates/`：单页模板（single-immigration-project.html）、列表模板（archive-immigration-project.html）
  - `patterns/`：可复用的设计模式（从 Figma 转换）
  - `parts/`：头部、底部等复用部分

### 插件部分（Immigration Projects Manager）

- **功能模块**：
  - 注册自定义文章类型 `immigration_project`
  - 注册分类法（taxonomy）：`project_category`（项目类别）、`project_type`（项目类型）
  - ACF 字段组配置（项目详情数据）
  - REST API 端点（用于 AJAX 筛选）
  - 自定义块注册（项目卡片块、筛选器块）

### 自定义块开发（React）

1. **Immigration Project Card Block**（项目卡片块）
2. **Immigration Projects Filter Block**（筛选器块 - 带 AJAX）
3. **Immigration Project Details Block**（详情信息卡片块）
4. **Immigration Project Tabs Block**（标签页内容块）
5. **Related Projects Block**（相关项目推荐块）

## 二、完整页面架构

### 页面类型清单

1. **首页**：FSE 模板 + Block Patterns
2. **关于页**：标准页面模板
3. **联系页**：标准页面 + Contact Form 7
4. **博客分类页**：`archive.html` 模板
5. **博客详情页**：`single.html` 模板
6. **咨询表单**：独立页面（Form 插件或 ACF）
7. **项目分类页**：`archive-immigration_project.html` + 筛选器块
8. **项目详情页**：`single-immigration_project.html` + ACF 字段
9. **成功案例页**：自定义文章类型 `success_case` + 列表模板
10. **预约页**：标准页面 + 预约表单块
11. **报价分类页**：自定义文章类型 `pricing` + 列表模板
12. **报价详情页**：`single-pricing.html`
13. **打分分类页**：标准页面 + 打分工具列表
14. **打分详情页**：页面模板 + 打分工具块（集成现有 HTML+JS）

## 三、开发流程详解（React 编译与部署）

### 标准 WordPress + React 开发流程

```
工作目录：/Users/ryan/Local Sites/shawglobal/app/public/wp-content/

1. 源码开发（编辑这里）
   ├── themes/shawglobal-theme/src/
   └── plugins/immigration-projects-manager/blocks/xxx/src/

2. 实时编译（开发时运行）
   $ npm run start  # 监听文件变化，自动编译

3. 生产编译（发布前）
   $ npm run build  # 生成压缩优化后的文件

4. WordPress 加载（自动）
   ├── themes/shawglobal-theme/build/
   └── plugins/immigration-projects-manager/blocks/xxx/build/
```

### 详细操作步骤

**Step 1：初始化项目**

```bash
cd /Users/ryan/Local Sites/shawglobal/app/public/wp-content/themes/shawglobal-theme
npm init -y
npm install @wordpress/scripts --save-dev
```

**Step 2：配置 package.json**

```json
{
  "scripts": {
    "start": "wp-scripts start",
    "build": "wp-scripts build",
    "lint:js": "wp-scripts lint-js"
  }
}
```

**Step 3：开发时运行**

```bash
npm run start
# 终端保持运行，编辑 src/ 文件会自动编译到 build/
# LocalWP 的浏览器刷新即可看到效果
```

**Step 4：生产编译**

```bash
npm run build
# 生成优化后的文件，准备部署
```

### 文件组织结构

```
shawglobal-theme/
├── src/                    # ✏️ 开发时编辑这里
│   ├── blocks/
│   │   ├── project-card/
│   │   │   ├── index.js    # 块入口
│   │   │   ├── edit.js     # React 组件
│   │   │   └── style.scss
│   │   └── scoring-tool/   # 打分工具块
│   ├── styles/
│   │   └── main.scss
│   └── scripts/
│       └── main.js
├── build/                  # 🤖 自动生成（不手动编辑）
│   ├── blocks/
│   │   ├── project-card/
│   │   │   ├── index.js
│   │   │   └── style.css
│   │   └── scoring-tool/
│   ├── style.css
│   └── main.js
├── templates/              # FSE 模板
├── patterns/               # Block Patterns
├── functions.php           # 加载 build/ 的文件
└── package.json
```

### PHP 加载编译后的资源

```php
// functions.php
function shawglobal_enqueue_assets() {
    // 加载编译后的 CSS
    wp_enqueue_style(
        'shawglobal-main',
        get_template_directory_uri() . '/build/style.css',
        [],
        filemtime(get_template_directory() . '/build/style.css')
    );
    
    // 加载编译后的 JS
    wp_enqueue_script(
        'shawglobal-main',
        get_template_directory_uri() . '/build/main.js',
        ['wp-element', 'wp-blocks'],
        filemtime(get_template_directory() . '/build/main.js'),
        true
    );
}
add_action('wp_enqueue_scripts', 'shawglobal_enqueue_assets');
```

## 四、快速脚手架方案

### Step 1: 创建主题骨架

```bash
# 使用 WP-CLI 创建空白 FSE 主题
wp scaffold block-theme shawglobal-theme \
  --theme_name="Shaw Global Immigration Theme" \
  --activate

# 或者手动创建基于 Twenty Twenty-Five 的子主题
```

**主题目录结构**：

```
wp-content/themes/shawglobal-theme/
├── theme.json              # 设计系统配置
├── style.css               # 主题元信息
├── functions.php           # 主题函数
├── templates/
│   ├── index.html
│   ├── single-immigration_project.html
│   └── archive-immigration_project.html
├── parts/
│   ├── header.html
│   └── footer.html
├── patterns/
│   ├── hero-banner.php
│   ├── project-info-card.php
│   └── contact-form.php
├── assets/
│   ├── src/
│   │   ├── blocks/         # React 块源码
│   │   ├── styles/         # SCSS 样式
│   │   └── scripts/        # JavaScript
│   └── build/              # 编译后文件
└── package.json
```

### Step 2: 创建插件骨架

```bash
# 使用 WP-CLI 创建插件
wp scaffold plugin immigration-projects-manager \
  --plugin_name="Immigration Projects Manager" \
  --plugin_description="Manages immigration project custom post types and ACF fields" \
  --activate
```

**插件目录结构**：

```
wp-content/plugins/immigration-projects-manager/
├── immigration-projects-manager.php    # 主插件文件
├── includes/
│   ├── class-post-type.php            # CPT 注册
│   ├── class-taxonomies.php           # 分类法注册
│   ├── class-acf-fields.php           # ACF 字段配置
│   ├── class-rest-api.php             # REST API 端点
│   └── class-blocks.php               # 块注册
├── blocks/
│   ├── project-card/
│   │   ├── src/
│   │   │   ├── edit.js                # React 编辑器组件
│   │   │   ├── save.js                # 前端保存
│   │   │   ├── style.scss             # 样式
│   │   │   └── index.js
│   │   ├── block.json
│   │   └── render.php                 # 动态渲染
│   ├── projects-filter/
│   └── related-projects/
├── acf-json/                          # ACF 字段导出
└── package.json
```

### Step 3: 初始化开发环境

```bash
# 在主题目录
cd wp-content/themes/shawglobal-theme
npm init -y
npm install @wordpress/scripts @wordpress/block-editor @wordpress/components --save-dev

# 在插件目录
cd wp-content/plugins/immigration-projects-manager
npm init -y
npm install @wordpress/scripts --save-dev
```

## 三、核心功能实现

### 1. 自定义文章类型配置（插件）

**文件**：`includes/class-post-type.php`

```php
register_post_type('immigration_project', [
    'labels' => [...],
    'public' => true,
    'has_archive' => true,
    'show_in_rest' => true,  // 必须，用于块编辑器
    'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
    'rewrite' => ['slug' => 'projects'],
]);
```

**分类法**：

- `project_country`（国家/地区）：加拿大、土耳其、安提瓜和巴布达、希腊、新加坡、日本、美国、菲律宾、葡萄牙、全部
- `project_category`（移民项目分类）：创业移民、技术移民、投资移民、置业移民

### 2. ACF 字段组设计

根据截图，需要的字段：

```php
// 基本信息组
- processing_period (文本)
- identity_type (文本)
- investment_amount (文本)
- residential_requirement (文本)
- language_requirement (文本)

// 详情内容组（使用 Tabs 字段）
- project_overview (WYSIWYG 编辑器)
- project_advantages (Repeater: icon + title + description)
- application_requirements (WYSIWYG 编辑器)
- application_process (Repeater: step_number + content)
- about_life (图片库 + 描述)

// Banner 组
- banner_image (图片)
- banner_subtitle (文本)
```

### 3. AJAX 筛选功能

**REST API 端点**（插件 `class-rest-api.php`）：

```php
register_rest_route('immigration/v1', '/projects', [
    'methods' => 'GET',
    'callback' => 'filter_projects',
    'args' => [
        'category' => ['type' => 'string'],
        'type' => ['type' => 'string'],
    ],
]);
```

**筛选器块**（React）：

```javascript
// blocks/projects-filter/src/edit.js
const FilterBlock = () => {
  const [categories, setCategories] = useState([]);
  const [projects, setProjects] = useState([]);
  
  const handleFilter = async (categoryId, typeId) => {
    const response = await apiFetch({
      path: `/immigration/v1/projects?category=${categoryId}&type=${typeId}`
    });
    setProjects(response);
  };
  
  return (
    <div className="immigration-filter">
      {/* 两级分类下拉框 */}
      {/* 项目列表展示 */}
    </div>
  );
};
```

### 4. Figma to Pattern 工作流

**方案 A：半自动化**

1. 从 Figma 导出 HTML/CSS（使用 Figma to HTML 插件）
2. 手动转换为 WordPress Block Pattern
3. 保存到 `patterns/` 目录

**方案 B：使用 Figma API**

1. 安装 `figma-js` 库获取设计数据
2. 编写转换脚本将 Figma 组件转为 Block Markup
3. 自动生成 pattern PHP 文件

**Pattern 示例**：

```php
// patterns/project-info-card.php
<?php
return [
    'title' => __('Project Info Card', 'shawglobal-theme'),
    'categories' => ['immigration'],
    'content' => '
        <!-- wp:group {"className":"project-info-card"} -->
        <div class="wp-block-group project-info-card">
            <!-- ACF 字段显示 -->
        </div>
        <!-- /wp:group -->
    ',
];
```

## 四、详情页模板设计

**模板文件**：`templates/single-immigration_project.html`

使用 FSE 模板语法：

```html
<!-- wp:template-part {"slug":"header"} /-->

<!-- wp:cover {"url":"ACF_FIELD_banner_image"} -->
<div class="wp-block-cover">
    <h1><!-- wp:post-title /--></h1>
    <p>ACF_FIELD_banner_subtitle</p>
</div>
<!-- /wp:cover -->

<!-- wp:columns -->
<div class="wp-block-columns">
    <!-- 左侧内容 -->
    <div class="wp-block-column">
        <!-- 信息卡片 -->
        <!-- 标签页内容 -->
    </div>
    
    <!-- 右侧表单 -->
    <div class="wp-block-column">
        <!-- 联系表单 -->
    </div>
</div>
<!-- /wp:columns -->

<!-- 相关项目推荐 -->
<!-- wp:immigration/related-projects /-->

<!-- wp:template-part {"slug":"footer"} /-->
```

## 五、开发步骤总结

### Phase 1: 基础搭建（插件优先）

1. 创建插件目录结构
2. 注册 CPT 和 Taxonomies
3. 配置 ACF 字段组
4. 测试数据录入

### Phase 2: 主题开发

1. 创建 FSE 主题骨架
2. 配置 `theme.json`（颜色、字体等）
3. 创建模板文件（single、archive）
4. 开发 Block Patterns

### Phase 3: React 块开发

1. 项目卡片块（用于列表页）
2. 筛选器块（AJAX 功能）
3. 详情信息块
4. 相关项目块

### Phase 4: 样式与交互

1. 响应式样式调整
2. AJAX 筛选逻辑完善
3. 动画和过渡效果
4. 浏览器兼容性测试

### Phase 5: Figma 集成

1. 导出 Figma 设计为 HTML/CSS
2. 转换为 Block Patterns
3. 整合到主题中

## 六、推荐工具与资源

### 开发工具

- **LocalWP**：本地开发环境（已安装）
- **WP-CLI**：命令行工具
- **ACF Pro**：高级自定义字段（需购买）
- **Query Monitor**：调试插件

### Figma 转换

- **Figma to WordPress** (付费插件)
- **Anima** (Figma 插件，导出 React 组件)
- **手动转换**：使用 Inspect 面板获取样式

### 代码编辑器插件

- **WordPress Snippets** (VSCode)
- **PHP Intelephense**
- **ES7+ React/Redux Snippets**

## 七、注意事项

1. **ACF 字段**：使用 ACF JSON 同步功能，将字段配置导出为 JSON 文件便于版本控制
2. **REST API 安全**：添加 nonce 验证和权限检查
3. **性能优化**：

   - 使用 WP Query 缓存
   - 图片懒加载
   - 块资源按需加载（`viewScript`）

4. **多语言支持**：预留 WPML 或 Polylang 集成
5. **SEO**：使用 Yoast SEO，确保自定义字段可被索引

### 打分工具（HTML+JS）集成方案

**方案 A：短代码集成（最简单）**

1. 创建插件文件：
```php
// plugins/immigration-scoring/immigration-scoring.php
<?php
/*
Plugin Name: Immigration Scoring Tool
*/

function immigration_scoring_enqueue() {
    wp_enqueue_script(
        'scoring-tool',
        plugin_dir_url(__FILE__) . 'assets/scoring-tool.js',
        [],
        '1.0.0',
        true
    );
    wp_enqueue_style(
        'scoring-tool',
        plugin_dir_url(__FILE__) . 'assets/scoring-tool.css'
    );
}
add_action('wp_enqueue_scripts', 'immigration_scoring_enqueue');

function scoring_tool_shortcode() {
    ob_start();
    ?>
    <div id="immigration-scoring-app">
        <!-- 您原有的 HTML 结构 -->
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('immigration_scoring', 'scoring_tool_shortcode');
```

2. 使用：在任何页面插入 `[immigration_scoring]` 短代码

**方案 B：自定义块（更现代）**

```javascript
// blocks/scoring-tool/src/index.js
import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';

registerBlockType('shawglobal/scoring-tool', {
    title: 'Immigration Scoring Tool',
    category: 'widgets',
    edit: Edit,
    save, // 或使用 render.php 动态渲染
});
```

```javascript
// blocks/scoring-tool/src/edit.js
import { useBlockProps } from '@wordpress/block-editor';
import ScoringToolComponent from './components/ScoringTool'; // 您的 React 组件

export default function Edit() {
    return (
        <div {...useBlockProps()}>
            <ScoringToolComponent />
        </div>
    );
}
```

**方案 C：页面模板（适合独立页面）**

```php
// templates/template-scoring.php
<?php
/* Template Name: Immigration Scoring Tool */
get_header();
?>

<div id="scoring-app"></div>

<script>
    // 您原有的 JS 逻辑
    // 或者加载外部文件
</script>

<?php get_footer(); ?>
```

**推荐：方案 B（自定义块）**，原因：

- ✅ 可在任何页面通过块编辑器插入
- ✅ 支持 React 组件化开发
- ✅ 便于维护和版本控制
- ✅ 可配置块属性（如显示哪种打分类型）

### To-dos

- [ ] 创建插件骨架并注册 CPT、Taxonomies 和 ACF 字段组
- [ ] 创建 FSE 主题骨架，配置 theme.json 和基础模板
- [ ] 使用 React 开发自定义块（项目卡片、筛选器、详情组件）
- [ ] 实现 REST API 端点和 AJAX 筛选功能
- [ ] 创建详情页和列表页模板，开发 Block Patterns
- [ ] 实现响应式样式和交互效果
- [ ] 从 Figma 导出设计并转换为 Block Patterns