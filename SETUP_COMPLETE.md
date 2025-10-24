# Shaw Global WordPress 项目设置完成

## 已完成的工作

### ✅ 1. Immigration Projects Manager 插件

**位置**: `wp-content/plugins/immigration-projects-manager/`

**功能**:
- ✅ 自定义文章类型 `immigration_project`
- ✅ 两个分类法：
  - `project_country` (国家)
  - `project_category` (项目分类)
- ✅ 完整的 ACF 字段组配置：
  - 基本信息（处理周期、身份类型、投资金额等）
  - 项目详情（优势、申请要求、流程等）
  - Banner 配置
- ✅ REST API 端点用于前端筛选和数据获取

**文件结构**:
```
immigration-projects-manager/
├── immigration-projects-manager.php  (主插件文件)
├── includes/
│   ├── class-post-type.php          (CPT 注册)
│   ├── class-taxonomies.php         (分类法注册)
│   ├── class-acf-fields.php         (ACF 字段配置)
│   └── class-rest-api.php           (REST API)
└── README.md
```

### ✅ 2. Shaw Global FSE 主题

**位置**: `wp-content/themes/shawglobal-theme/`

**功能**:
- ✅ Full Site Editing (FSE) 支持
- ✅ 完整的 theme.json 配置（颜色、字体、间距）
- ✅ 模板部分（header, footer）
- ✅ 页面模板（首页、项目列表、项目详情）
- ✅ 两个 React 自定义块：
  - Immigration Project Card (项目卡片)
  - Immigration Projects Filter (项目筛选器)

**文件结构**:
```
shawglobal-theme/
├── style.css                    (主题元数据)
├── theme.json                   (主题配置)
├── functions.php                (主题函数)
├── templates/                   (FSE 模板)
│   ├── index.html
│   ├── single-immigration_project.html
│   └── archive-immigration_project.html
├── parts/                       (模板部分)
│   ├── header.html
│   └── footer.html
├── src/                         (源代码)
│   ├── blocks/                  (React 块)
│   ├── scripts/
│   └── styles/
├── build/                       (编译后的文件)
└── package.json
```

### ✅ 3. React 开发环境

- ✅ npm 依赖已安装
- ✅ 代码已成功编译
- ✅ 自定义块已编译到 `build/blocks/` 目录

## 接下来的步骤

### 在 WordPress 后台操作：

1. **激活主题**
   - 进入 外观 > 主题
   - 激活 "Shaw Global Immigration Theme"

2. **激活插件**
   - 进入 插件
   - 激活 "Immigration Projects Manager"
   - **重要**: 确保已安装并激活 **Advanced Custom Fields Pro** 插件

3. **创建测试项目**
   - 进入 Immigration Projects > 添加新项目
   - 填写标题、内容和 ACF 自定义字段
   - 选择国家和项目分类
   - 发布项目

4. **测试自定义块**
   - 创建或编辑页面
   - 在编辑器中搜索 "Immigration" 查找自定义块：
     - Immigration Project Card
     - Immigration Projects Filter
   - 插入块并配置

### 开发命令

在主题目录 (`wp-content/themes/shawglobal-theme/`) 中：

```bash
# 开发模式（实时编译，热重载）
npm run start

# 生产编译
npm run build

# 代码检查
npm run lint:js
npm run lint:css
```

## 项目特性

### 自定义块功能

#### Immigration Project Card
- 选择要显示的项目
- 显示/隐藏摘要
- 显示/隐藏元信息
- 响应式卡片设计

#### Immigration Projects Filter
- 按国家筛选
- 按项目分类筛选
- AJAX 加载（无需刷新页面）
- 可配置列数和每页项目数
- 网格布局，响应式

### REST API 端点

插件提供以下 REST API 端点：

- `GET /wp-json/immigration/v1/projects` - 获取项目列表（支持筛选）
- `GET /wp-json/immigration/v1/projects/{id}` - 获取单个项目详情
- `GET /wp-json/immigration/v1/filters` - 获取筛选选项（国家、分类）

### ACF 字段组

#### 基本信息
- 处理周期 (processing_period)
- 身份类型 (identity_type)
- 投资金额 (investment_amount)
- 居住要求 (residential_requirement)
- 语言要求 (language_requirement)

#### 项目详情
- 项目概述 (project_overview)
- 项目优势 (project_advantages) - 可重复字段
- 申请要求 (application_requirements)
- 申请流程 (application_process) - 可重复字段
- 关于生活 (about_life) - 图库

#### Banner
- Banner 图片 (banner_image)
- Banner 副标题 (banner_subtitle)

## 故障排除

### 如果自定义块不显示：

1. 确保已运行 `npm run build`
2. 清除浏览器缓存
3. 检查 WordPress 调试日志

### 如果 ACF 字段不显示：

1. 确保 ACF Pro 插件已激活
2. 重新激活 Immigration Projects Manager 插件
3. 检查插件文件权限

### 如果样式未加载：

1. 确保 build 目录存在并包含编译后的文件
2. 清除 WordPress 缓存
3. 检查主题 functions.php 中的文件路径

## 技术栈

- **WordPress**: 6.0+
- **PHP**: 7.4+
- **Node.js**: 18+
- **React**: 18.x
- **WordPress Scripts**: 27.x
- **SCSS**: 用于样式
- **Webpack**: 5.x (通过 @wordpress/scripts)

## 文件位置快速参考

| 文件类型 | 位置 |
|---------|------|
| 插件主文件 | `wp-content/plugins/immigration-projects-manager/immigration-projects-manager.php` |
| 主题主文件 | `wp-content/themes/shawglobal-theme/functions.php` |
| 主题配置 | `wp-content/themes/shawglobal-theme/theme.json` |
| React 块源码 | `wp-content/themes/shawglobal-theme/src/blocks/` |
| 编译后的块 | `wp-content/themes/shawglobal-theme/build/blocks/` |
| 页面模板 | `wp-content/themes/shawglobal-theme/templates/` |
| ACF 字段配置 | `wp-content/plugins/immigration-projects-manager/includes/class-acf-fields.php` |

## 完成状态

- ✅ 插件骨架并注册 CPT、Taxonomies 和 ACF 字段组
- ✅ 创建 FSE 主题骨架，配置 theme.json 和基础模板
- ✅ 使用 React 开发自定义块（项目卡片、筛选器、详情组件）
- ✅ npm 依赖安装完成
- ✅ React 代码编译成功

**现在可以在 WordPress 后台激活主题和插件，开始使用了！**
