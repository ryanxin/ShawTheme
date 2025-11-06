# Elementor Theme Builder 不显示修复指南

如果你的 Immigration Project 详情页显示 404 或空白页面，请按以下步骤修复。

## 问题诊断

### 症状
- 点击 Immigration Project 显示 404 页面
- 或显示空白页面
- Elementor Theme Builder 模板没有应用

### 根本原因
1. Elementor 需要正确识别 CPT
2. Theme Builder 条件设置不正确
3. 固定链接没有刷新

---

## 完整修复步骤

### 步骤1: 停用和重新激活所有相关插件

这会强制 WordPress 重新初始化所有东西。

**顺序很重要！**

1. **停用 Shaw Immigration Projects**
   - WordPress后台 → 插件 → 已安装插件
   - 找到 "Shaw Immigration Projects"
   - 点击 "停用"
   - 等待2秒

2. **停用 Elementor**
   - 找到 "Elementor"
   - 点击 "停用"
   - 等待2秒

3. **停用 Elementor Pro**（如果有）
   - 找到 "Elementor Pro"
   - 点击 "停用"
   - 等待2秒

4. **重新激活 Elementor**
   - 再次找到 "Elementor"
   - 点击 "激活"
   - 等待3秒

5. **重新激活 Elementor Pro**（如果有）
   - 再次找到 "Elementor Pro"
   - 点击 "激活"
   - 等待3秒

6. **重新激活 Shaw Immigration Projects**
   - 再次找到 "Shaw Immigration Projects"
   - 点击 "激活"
   - 等待3秒

### 步骤2: 清除所有缓存

**WordPress 缓存：**
- 如果有缓存插件（WP Super Cache, W3 Total Cache等）
- 找到缓存设置
- 点击 "清除所有缓存"

**浏览器缓存：**
- 按 **Ctrl+F5** (Windows) 或 **Cmd+Shift+R** (Mac)
- 或按 **Ctrl+Shift+Delete** 打开清除浏览器缓存对话框

**服务器缓存：**
- 如果使用了 CloudFlare 等 CDN
- 清除 CDN 缓存

### 步骤3: 刷新固定链接

这是**最重要**的一步！

1. WordPress后台 → **设置** → **固定链接**
2. 直接点击 **"保存更改"**（不需要修改任何设置）
3. 会看到 "固定链接已更新"

这会生成正确的 URL 重写规则。

### 步骤4: 检查 Elementor 是否识别了 CPT

1. 打开 Elementor
2. **Templates** → **Theme Builder**
3. 查看左侧是否显示了 "Immigration Project" 选项

如果看到了，继续步骤5。
如果**没有看到**，说明 CPT 没有正确注册，请参考"CPT 未被识别"章节。

### 步骤5: 创建或更新 Single 模板

#### 新建 Single 模板

1. **Templates** → **Theme Builder**
2. 点击 **"+ New Template"**
3. 选择 **"Single"**
4. 输入名称，如 "Immigration Project Single"
5. 点击 **"Create Template"**

#### 设置条件

1. 右上角 → **Settings**（或三个点菜单）
2. 找到 **"Display Conditions"** 或 **"Location"**
3. 点击 **"+ Add Condition"**
4. 选择：
   - **Post Type** 
   - **is equal to**
   - **Immigration Project**
5. 点击 **"Save"**

#### 设计模板

在 Elementor 编辑器中：

1. **Hero 区域**：
   - 添加 Section（Full Width）
   - 添加 Heading → 插入动态标签 → Post Title
   - 添加 Text → 插入动态标签 → Post Excerpt

2. **内容区域**：
   - 添加 Heading（子标题）
   - 添加 Text Editor → 插入动态标签 → Post Content

3. **ACF 字段**：
   - 添加 Text → 插入动态标签 → ACF Field → processing_period
   - 添加 Text → 插入动态标签 → ACF Field → identity_type
   - 等等...

4. **保存模板**
   - 点击 **"Update"** 或 **"Publish"**

### 步骤6: 验证修复

1. 返回 WordPress 后台
2. 点击 **Immigration Projects**
3. 打开任何一个项目
4. 查看右上方的 **"View Project"** 链接
5. 在新标签页打开

**应该看到：**
✅ Elementor 设计的页面
✅ 所有内容正确显示
✅ 没有 404 错误

---

## 故障排除

### 问题：还是显示 404

**原因和解决方案：**

1. **固定链接没有正确刷新**
   - 再次打开 设置 → 固定链接
   - 点击"保存更改"
   - 等待页面刷新

2. **PHP 模板文件还在冲突**
   - 检查主题文件夹中是否有：
     - `single.php`
     - `single-immigration_project.php`
   - 如果有，将其重命名为 `.bak` 或删除

3. **CPT 没有设置 `publicly_queryable`**
   - 检查插件代码中 CPT 注册是否有：
     ```php
     'publicly_queryable' => true,
     ```
   - 如果没有，需要添加

### 问题：Elementor 看不到 Immigration Project CPT

1. **检查 Elementor 设置**
   - Elementor → 设置 → 常规
   - 向下滚动找到 "Post Types"
   - 确保 "Immigration Project" 被选中
   - 如果没有看到，可能是 CPT 没有设置正确

2. **检查 CPT 是否正确注册**
   - 在 WordPress 后台
   - 应该在左侧菜单看到 "Immigration Projects"
   - 能够创建/编辑项目

3. **强制刷新 CPT 识别**
   - 停用 Shaw Immigration Projects 插件
   - 访问任何 WordPress 页面（这会清除内部缓存）
   - 重新激活插件
   - 现在 Elementor 应该识别了

### 问题：仍然显示旧的 PHP 模板样式

1. **确认插件已禁用 PHP 模板加载**
   - 检查 `shaw-immigration-projects.php` 第 50 行
   - 应该看到被注释的代码：
     ```php
     // add_filter('single_template', array($this, 'load_custom_template'));
     ```

2. **强制清除缓存**
   - 浏览器：Ctrl+Shift+Delete
   - WordPress 缓存插件：清除所有缓存
   - CDN 缓存：清除

3. **如果还是不行**
   - 关闭浏览器
   - 等待10秒
   - 重新打开浏览器
   - 访问项目页面

---

## 快速诊断表

如果还是有问题，按照这个表检查：

| 症状 | 检查项 | 修复方法 |
|------|--------|---------|
| 显示 404 | 固定链接 | 刷新固定链接 |
| 显示 404 | CPT 配置 | 检查 publicly_queryable |
| 显示空白 | 模板条件 | 重新设置 Display Conditions |
| 显示 PHP 样式 | 模板加载器 | 确认过滤器被禁用 |
| Elementor 看不到 CPT | Elementor 设置 | 检查 Post Types 选择 |

---

## 技术细节

### CPT 所需的参数

为了让 Elementor 正确识别和显示 CPT，需要这些参数：

```php
$args = array(
    'public'              => true,      // ✅ 必须
    'show_ui'             => true,      // ✅ 必须
    'publicly_queryable'  => true,      // ✅ 必须
    'has_archive'         => true,      // ✅ 必须
    'rewrite'             => array(
        'slug' => 'immigration-projects'  // ✅ 必须
    ),
    'show_in_rest'        => true,      // ✅ Elementor 需要
);
```

### Elementor Theme Builder 识别流程

1. Elementor 扫描所有已注册的 CPT
2. CPT 必须满足特定条件才能显示在 Theme Builder 中
3. 条件包括：public, show_ui, publicly_queryable 都为 true
4. 必须刷新固定链接才能让 WordPress 生成正确的 URL 规则

---

## 需要帮助？

如果按照所有步骤还是不行：

1. 提供屏幕截图（显示问题）
2. 告诉我看到的具体内容（404? 空白? 其他?)
3. 检查浏览器控制台是否有 JavaScript 错误：
   - F12 打开开发者工具
   - Console 标签
   - 截图任何红色错误

---

**最后更新**: 2024年
**适用版本**: Shaw Immigration Projects 1.0.0+, Elementor 3.0+

