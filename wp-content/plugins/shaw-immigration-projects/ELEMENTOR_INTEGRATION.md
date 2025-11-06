# Elementor Integration Guide

完整的 Elementor 集成指南,包含列表页和详情页的实现方法。

## 目录

1. [列表页实现方式](#列表页实现方式)
2. [详情页实现方式](#详情页实现方式)
3. [字段映射参考](#字段映射参考)
4. [样式定制](#样式定制)

---

## 列表页实现方式

### 方式一:AJAX双层Tab筛选(推荐)

这种方式实现了Figma设计中的双层Tab切换,通过AJAX异步加载,无需刷新页面。

#### 步骤:

1. **创建归档页面**
   - WordPress后台 → 页面 → 新建页面
   - 使用 Elementor 编辑
   - 或使用 Elementor → Templates → Theme Builder → Archive

2. **添加HTML结构**
   - 添加一个 **HTML 小部件**
   - 粘贴以下代码:

```html
<div class="shaw-immigration-filter">
    
    <!-- Loading Indicator -->
    <div class="shaw-loading" style="display: none;"></div>
    
    <!-- First Level: Countries -->
    <div class="shaw-country-tabs">
        <button class="shaw-country-tab active" data-country="all">All</button>
        <button class="shaw-country-tab" data-country="canada">Canada</button>
        <button class="shaw-country-tab" data-country="turkey">Turkey</button>
        <button class="shaw-country-tab" data-country="greece">Greece</button>
        <button class="shaw-country-tab" data-country="united-states">United States</button>
        <!-- 添加更多国家 -->
    </div>
    
    <!-- Second Level: Categories -->
    <div class="shaw-category-tabs">
        <button class="shaw-category-tab active" data-category="all">All</button>
        <button class="shaw-category-tab" data-category="entrepreneur-immigration">Entrepreneur Immigration</button>
        <button class="shaw-category-tab" data-category="skilled-worker-immigration">Skilled Worker Immigration</button>
        <button class="shaw-category-tab" data-category="investment-immigration">Investment Immigration</button>
        <button class="shaw-category-tab" data-category="real-estate-immigration">Real Estate Immigration</button>
    </div>
    
    <!-- Projects Container -->
    <div class="shaw-projects-grid">
        <div style="text-align: center; padding: 60px 20px;">
            <p style="font-size: 18px; color: #707070;">Loading projects...</p>
        </div>
    </div>
    
</div>
```

3. **发布页面**
   - JavaScript 和 CSS 会自动加载
   - Tab 切换和项目加载自动工作

#### 注意事项:

- `data-country` 和 `data-category` 属性必须使用正确的分类法slug
- 确保已创建对应的Countries和Categories分类
- Tab按钮的文本可以自由修改,但`data-*`属性必须匹配实际的slug

---

### 方式二:Elementor Loop Grid(静态筛选)

使用 Elementor 的 Loop Grid 小部件展示项目列表。

#### 步骤:

1. **添加Loop Grid小部件**
   - 在 Elementor 编辑器中搜索 "Loop Grid"
   - 拖放到页面

2. **配置Query(查询)**
   ```
   Source: Posts
   Post Type: Immigration Project
   Posts Per Page: 9
   Order By: Date
   Order: DESC
   ```

3. **配置Layout(布局)**
   ```
   Columns: 3 (桌面)
   Columns (Tablet): 2
   Columns (Mobile): 1
   Gap: 40px
   ```

4. **配置Loop Item Template(循环项模板)**
   
   在Loop Grid的"Template"选项中,创建或选择一个模板,添加以下元素:

   **图片:**
   - 添加 Image 小部件
   - 动态标签 → ACF Field → `card_image`
   - 尺寸: Large
   - 长宽比: 16:9
   
   **标题:**
   - 添加 Heading 小部件
   - 动态标签 → Post Title
   - HTML标签: H3
   - 字体大小: 28px
   
   **简短描述:**
   - 添加 Text Editor 小部件
   - 动态标签 → ACF Field → `short_description`
   - 字体大小: 18px
   
   **元数据:**
   - 添加多个 Text Editor 小部件
   - 处理周期: 动态标签 → ACF Field → `processing_period`
   - 身份类型: 动态标签 → ACF Field → `identity_type`
   
   **按钮:**
   - 添加 Button 小部件
   - 文本: "Get a Quote"
   - 链接: 动态标签 → Post URL
   - 样式: 圆角, 背景色 #E19A58

5. **添加筛选(可选)**
   
   在Loop Grid上方添加Taxonomy Filter小部件(需要JetSmartFilters等插件)

---

## 详情页实现方式

### 方式一:使用插件提供的模板(默认)

插件已包含一个完整的单页模板 `templates/single-immigration-project.php`,会自动应用。

**特点:**
- 自动显示所有ACF字段
- 包含Hero区域、Info卡片、Tab导航
- 侧边栏联系表单
- 相关项目推荐

**定制方法:**
1. 复制 `templates/single-immigration-project.php` 到主题目录
2. 重命名为 `single-immigration-project.php`
3. 根据需要修改

---

### 方式二:使用Elementor Theme Builder(推荐)

完全使用 Elementor 可视化编辑器定制详情页。

#### 步骤:

1. **创建Single模板**
   - Elementor → Templates → Theme Builder
   - Add New → Single
   - 选择条件: Post Type → Immigration Project

2. **Hero部分**
   
   添加一个Section:
   ```
   布局: Full Width
   高度: 670px
   背景: 动态标签 → Featured Image
   叠加层: 渐变 rgba(26,20,72,0.6) → rgba(26,20,72,0.3)
   ```
   
   在Section内添加:
   - **Heading**: 动态标签 → Post Title
     - 颜色: rgba(255,255,255,0.85)
     - 字体大小: 52px
     - 文本对齐: 居中
     - 文本阴影: 启用
   
   - **Text Editor**: 动态标签 → ACF Field → `short_description`
     - 颜色: 白色
     - 字体大小: 20px
     - 文本对齐: 居中

3. **Info Card(信息卡片)**
   
   添加Container,设置:
   ```
   背景: 白色
   边框半径: 8px
   内边距: 40px
   阴影: 0 4px 20px rgba(71,100,195,0.1)
   ```
   
   添加多个Text Editor:
   - **Processing Period**: 
     ```
     <div class="info-item">
       <span class="label">Processing Period:</span>
       <span class="value">[acf field="processing_period"]</span>
     </div>
     ```
   - 类似地添加其他字段(identity_type, investment_amount等)
   
   添加Button:
   - 文本: "Get a Quote"
   - 链接: #contact-form
   - 样式: 圆角50px,背景色#E19A58

4. **Tab导航**
   
   使用Tabs小部件或HTML小部件:
   ```html
   <div class="shaw-detail-tabs">
       <a href="#overview" class="shaw-detail-tab active">Project Overview</a>
       <a href="#advantages" class="shaw-detail-tab">Project Advantages</a>
       <a href="#requirements" class="shaw-detail-tab">Application Requirements</a>
       <a href="#process" class="shaw-detail-tab">Application Process</a>
       <a href="#life" class="shaw-detail-tab">About Life</a>
   </div>
   ```

5. **内容区域**
   
   **Project Overview:**
   - Text Editor: 动态标签 → ACF Field → `project_overview`
   - Gallery: 动态标签 → ACF Gallery → `project_gallery`
   
   **Project Advantages:**
   - Repeater (需要Dynamic Content插件):
     - 动态标签 → ACF Repeater → `advantages`
     - 子字段: `advantage_text`
   - 或使用HTML:
     ```
     [acf field="advantages"]
     ```
   
   **Application Requirements:**
   - Text Editor: 动态标签 → ACF Field → `application_requirements`
   
   **Application Process:**
   - Text Editor: 动态标签 → ACF Field → `application_process`
   
   **About Life:**
   - Text Editor: 动态标签 → ACF Field → `about_life`
   - Repeater: 动态标签 → ACF Repeater → `life_media`

6. **侧边栏(Sidebar)**
   
   创建一个Sticky Container:
   ```
   宽度: 352px
   Position: Sticky
   Top: 100px
   ```
   
   **联系表单:**
   - 使用Contact Form 7 / Elementor Form / WPForms
   - 或添加Shortcode小部件:
     ```
     [contact-form-7 id="123"]
     ```
   
   **相关项目:**
   - Loop Grid小部件
   - Query设置:
     ```
     Source: Related
     Posts Per Page: 4
     Exclude: Current Post
     ```

7. **发布模板**
   - 保存并发布
   - 所有Immigration Project详情页自动应用此模板

---

## 字段映射参考

### ACF字段名称与用途

| ACF字段名 | 类型 | 用途 | Elementor动态标签 |
|-----------|------|------|-------------------|
| `processing_period` | Text | 处理周期 | ACF Field → processing_period |
| `identity_type` | Text | 身份类型 | ACF Field → identity_type |
| `investment_amount` | Text | 投资金额 | ACF Field → investment_amount |
| `residential_requirements` | Text | 居住要求 | ACF Field → residential_requirements |
| `language` | Text | 语言要求 | ACF Field → language |
| `card_image` | Image | 卡片图片 | ACF Field → card_image |
| `short_description` | Textarea | 简短描述 | ACF Field → short_description |
| `project_overview` | WYSIWYG | 项目概览 | ACF Field → project_overview |
| `project_gallery` | Gallery | 项目图库 | ACF Gallery → project_gallery |
| `advantages` | Repeater | 项目优势 | ACF Repeater → advantages |
| `application_requirements` | WYSIWYG | 申请要求 | ACF Field → application_requirements |
| `application_process` | WYSIWYG | 申请流程 | ACF Field → application_process |
| `about_life` | WYSIWYG | 关于生活 | ACF Field → about_life |
| `life_media` | Repeater | 生活媒体 | ACF Repeater → life_media |
| `is_featured` | True/False | 特色项目 | ACF Field → is_featured |

### Repeater字段子字段

**advantages (优势):**
- `advantage_text` - 优势文本

**life_media (生活媒体):**
- `title` - 标题
- `media_type` - 类型(image/video)
- `image` - 图片
- `video_url` - 视频URL

---

## 样式定制

### CSS变量

插件使用CSS变量,可以在主题中覆盖:

```css
:root {
    --shaw-primary: #1a1448;      /* 主色调 */
    --shaw-accent: #e19a58;       /* 强调色 */
    --shaw-white: #ffffff;        /* 白色 */
    --shaw-light-gray: #f4f6f7;   /* 浅灰色 */
    --shaw-text: #333333;         /* 文本色 */
    --shaw-text-light: #707070;   /* 浅文本色 */
    --shaw-border: #d1d4e5;       /* 边框色 */
    --shaw-transition: all 0.3s ease;  /* 过渡效果 */
}
```

### 在Elementor中添加自定义CSS

1. **页面级CSS:**
   - Elementor编辑器 → 设置图标 → Custom CSS
   
2. **全局CSS:**
   - Elementor → Custom CSS
   - 或主题 → 外观 → 自定义 → 额外CSS

### 常用样式覆盖示例

```css
/* 修改项目卡片样式 */
.shaw-project-card {
    border-radius: 12px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.1);
}

/* 修改Tab样式 */
.shaw-country-tab.active {
    border-bottom-color: #your-color;
    color: #your-color;
}

/* 修改按钮样式 */
.shaw-project-btn,
.shaw-cta-btn {
    background-color: #your-color;
}

.shaw-project-btn:hover,
.shaw-cta-btn:hover {
    background-color: #your-hover-color;
}
```

---

## 常见问题

### Q: 如何修改Tab的国家/类别列表?

**A:** 
1. 方式一(AJAX):在HTML中修改`data-country`和`data-category`属性
2. 方式二:在WordPress后台的Taxonomies中添加/删除term

### Q: 如何修改每页显示的项目数量?

**A:**
- AJAX方式:修改`ajax-filter.js`中的`per_page`参数
- Loop Grid方式:在Loop Grid设置中修改"Posts Per Page"

### Q: 联系表单如何处理提交?

**A:**
- 推荐使用Contact Form 7、Elementor Form或WPForms
- 也可以使用插件提供的示例表单并自定义处理函数

### Q: 如何使相关项目更智能?

**A:**
在`single-immigration-project.php`中修改`$related_args`,例如:
```php
'tax_query' => array(
    'relation' => 'OR',
    array(
        'taxonomy' => 'project_country',
        'field' => 'term_id',
        'terms' => $country_ids,
    ),
    array(
        'taxonomy' => 'project_category',
        'field' => 'term_id',
        'terms' => $category_ids,
    ),
),
```

### Q: 如何添加面包屑导航?

**A:**
使用Yoast SEO或Rank Math的面包屑功能,或添加Elementor的Breadcrumbs小部件

---

## 进阶技巧

### 1. 添加筛选器统计

在Tab按钮中显示项目数量:

```html
<button class="shaw-country-tab" data-country="canada">
    Canada <span class="count">(15)</span>
</button>
```

### 2. 添加加载动画

使用Elementor的动画效果:
- 选择元素 → Advanced → Motion Effects
- 选择Fade In、Slide Up等效果

### 3. 集成地图

在About Life部分添加Google Maps:
- 使用Elementor Pro的Google Maps小部件
- 或使用WP Google Maps等插件

### 4. 多语言支持

使用WPML或Polylang:
1. 翻译CPT和Taxonomies
2. 翻译ACF字段
3. Tab文本使用翻译函数

---

## 技术支持

如有问题,请联系 Shaw Global 技术支持。

