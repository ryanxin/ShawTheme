# Shaw Immigration Projects - 快速入门指南

5分钟快速上手指南 🚀

## 第一步:激活插件

1. WordPress后台 → 插件 → 已安装插件
2. 找到 "Shaw Immigration Projects"
3. 点击"激活"

✅ 插件会自动:
- 注册 Immigration Project 文章类型
- 创建 Countries 和 Categories 分类法
- 注册 ACF 字段组
- 注册 REST API 端点

## 第二步:设置分类法

### 添加国家(Countries)

WordPress后台 → Immigration Projects → Countries

**推荐添加:**
```
- Canada (slug: canada)
- United States (slug: united-states)
- Turkey (slug: turkey)
- Greece (slug: greece)
- Antigua and Barbuda (slug: antigua-and-barbuda)
- Singapore (slug: singapore)
- Japan (slug: japan)
- Philippines (slug: philippines)
- Portugal (slug: portugal)
```

### 添加项目类别(Categories)

WordPress后台 → Immigration Projects → Categories

**推荐添加:**
```
- Entrepreneur Immigration (slug: entrepreneur-immigration)
- Skilled Worker Immigration (slug: skilled-worker-immigration)
- Investment Immigration (slug: investment-immigration)
- Real Estate Immigration (slug: real-estate-immigration)
```

## 第三步:创建示例项目

WordPress后台 → Immigration Projects → 新建

### 基本信息

- **标题**: Canada Federal Innovation Immigration (SUV)
- **内容**: 输入项目的详细介绍
- **特色图片**: 上传一张代表性图片

### 分类

- **国家**: 选择 Canada
- **类别**: 选择 Entrepreneur Immigration

### ACF字段填写

**快速信息:**
- Processing Period: `20 months`
- Identity Type: `Permanent Resident`
- Investment Amount: `No mandatory requirements`
- Residential Requirements: `Five years, with two years fully completed`
- Language: `CLB5`

**列表页显示:**
- Card Featured Image: 上传卡片图片(建议16:9比例)
- Short Description: `A treasure trove immigration program to obtain permanent residency in one step.`

**详情页内容:**
- Project Overview: 输入完整的项目概述
- Project Gallery: 上传多张项目相关图片
- Advantages: 添加多个项目优势,例如:
  - `Low requirements for applicants`
  - `High success rate`
  - `One-time acquisition of permanent residency`
- Application Requirements: 列出申请要求
- Application Process: 说明申请流程
- About Life: 介绍目标国家的生活

**可选:**
- Life Media: 添加食物、学校、风景等媒体
- Featured Project: 勾选以标记为特色项目

### 发布

点击"发布"按钮

**重复此步骤创建更多项目**

## 第四步:创建列表页

### 方式A: Elementor Archive页面

1. Elementor → Templates → Theme Builder → Archive
2. Add New → Archive
3. 选择条件: Post Type Archive → Immigration Project
4. 使用Elementor编辑器
5. 添加 **HTML小部件**
6. 复制粘贴 `templates/archive-immigration-project.html` 的内容
7. 修改Tab中的国家和类别以匹配你创建的分类
8. 保存并发布

### 方式B: 创建普通页面

1. WordPress → 页面 → 新建
2. 标题: Immigration Projects
3. 使用Elementor编辑
4. 添加HTML小部件,粘贴Tab结构代码
5. 发布页面

### 测试筛选功能

- 访问列表页
- 点击不同的国家Tab
- 点击不同的类别Tab
- 确认项目列表实时更新(无页面刷新)

## 第五步:自定义详情页(可选)

插件已提供默认模板,如需定制:

### 方式A: 使用Elementor Theme Builder

1. Elementor → Templates → Theme Builder → Single
2. Add New → Single
3. 选择条件: Post Type → Immigration Project
4. 参考 `ELEMENTOR_INTEGRATION.md` 配置各个部分
5. 保存并发布

### 方式B: 使用主题模板

1. 复制 `templates/single-immigration-project.php`
2. 粘贴到主题目录: `your-theme/single-immigration-project.php`
3. 按需修改PHP代码

## 第六步:刷新永久链接

WordPress后台 → 设置 → 固定链接 → 点击"保存更改"

这会刷新WordPress的重写规则,确保所有URL正常工作。

---

## 快速检查清单

✅ **插件激活**
- [ ] Shaw Immigration Projects 已激活
- [ ] ACF (Advanced Custom Fields) 已激活

✅ **分类法设置**
- [ ] 至少添加了3个国家
- [ ] 至少添加了3个类别

✅ **示例内容**
- [ ] 创建了至少3个项目
- [ ] 每个项目都填写了必要的ACF字段
- [ ] 每个项目都分配了国家和类别

✅ **列表页**
- [ ] 创建了列表页面
- [ ] Tab筛选功能正常
- [ ] 项目卡片正常显示

✅ **详情页**
- [ ] 单个项目页面正常显示
- [ ] 所有ACF字段都正确显示
- [ ] 相关项目推荐正常

✅ **URL设置**
- [ ] 固定链接已刷新
- [ ] 项目URL正常访问

---

## 常用URL

- **列表页**: `yoursite.com/immigration-projects/`
- **单个项目**: `yoursite.com/immigration-projects/project-slug/`
- **国家归档**: `yoursite.com/project-country/canada/`
- **类别归档**: `yoursite.com/project-category/entrepreneur-immigration/`
- **REST API**: `yoursite.com/wp-json/shaw-immigration/v1/projects`

---

## 下一步

- 📖 阅读 `README.md` 了解完整功能
- 🎨 阅读 `ELEMENTOR_INTEGRATION.md` 学习Elementor集成
- 🎨 自定义CSS样式以匹配你的品牌
- 📝 添加更多项目内容
- 🌐 配置多语言(如需要)
- 📧 集成联系表单(Contact Form 7/Elementor Form)

---

## 需要帮助?

- 查看 `README.md` 获取详细文档
- 查看 `ELEMENTOR_INTEGRATION.md` 获取Elementor指南
- 联系 Shaw Global 技术支持

**祝你使用愉快! 🎉**

