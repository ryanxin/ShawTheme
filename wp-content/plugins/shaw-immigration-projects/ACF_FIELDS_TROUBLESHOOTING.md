# ACF 字段显示问题 - 诊断与修复

## 问题描述

创建新的 Immigration Project 时，ACF 自定义字段没有在编辑界面显示。

## 根本原因

ACF（Advanced Custom Fields）插件需要在特定的时机才能正确加载和显示字段组。之前的代码在 `init` 钩子中注册字段，但这个时机不正确。

## 解决方案

已修复主插件文件 `shaw-immigration-projects.php`，现在采用双重注册机制：

1. **主要方法**: 使用 `acf/init` 钩子（ACF提供的专用钩子）
2. **备用方法**: 使用 `plugins_loaded` 钩子作为后备

## 修复步骤

### 步骤1: 更新插件代码

代码已在 `shaw-immigration-projects.php` 中更新。确保你已保存最新版本。

### 步骤2: 停用并重新激活插件

1. **WordPress后台** → 插件 → 已安装插件
2. 找到 "Shaw Immigration Projects"
3. 点击 **"停用"**
4. 等待2-3秒
5. 页面刷新后，再点击 **"激活"**
6. 看到激活成功提示

### 步骤3: 清除WordPress缓存

如果你使用了缓存插件：

- **WP Super Cache**: 在插件页面点击"清除缓存"
- **W3 Total Cache**: Settings → Performance → Purge All Caches
- **其他缓存插件**: 按照各自指南清除缓存

如果使用了服务器缓存：
- 联系你的主机提供商清除缓存

### 步骤4: 刷新WordPress重写规则

1. WordPress后台 → **设置** → **固定链接**
2. 直接点击 **"保存更改"** 按钮（无需修改任何设置）
3. 这会刷新 WordPress 的重写规则缓存

### 步骤5: 验证字段已加载

1. WordPress后台 → **Immigration Projects** → **新建**
2. 页面应该显示以下几部分的 ACF 字段：
   - ✅ **Project Details** 标题下应该出现字段组
   - ✅ **Processing Period** 字段
   - ✅ **Identity Type** 字段
   - ✅ **Investment Amount** 字段
   - ✅ 等等...

如果看到这些字段，说明修复成功！🎉

## 如果问题仍然存在

### 检查清单

- [ ] 是否已安装并激活了 **Advanced Custom Fields (ACF)** 插件？
  - 如果没有，请先安装：WordPress后台 → 插件 → 添加插件 → 搜索 "Advanced Custom Fields" → 安装并激活

- [ ] 是否正确停用和重新激活了 Shaw Immigration Projects 插件？

- [ ] 是否清除了所有缓存？

- [ ] 是否刷新了固定链接？

### 调试步骤

1. **检查 ACF 是否加载**
   - WordPress后台 → 插件
   - 查找 "Advanced Custom Fields" - 应该显示为 "已激活"

2. **检查字段组是否存在**
   - WordPress后台 → 自定义字段（或 ACF）
   - 应该看到 "Project Details" 字段组
   - 打开它，确认所有字段都在里面

3. **检查字段组位置配置**
   - 打开 "Project Details" 字段组
   - 向下滚动到 "位置(Location)" 部分
   - 应该看到: "Post Type is equal to Immigration Project"

4. **强制刷新浏览器**
   - 按 **Ctrl+Shift+Del**（Windows）或 **Cmd+Shift+Delete**（Mac）
   - 选择清除浏览器缓存
   - 或在地址栏使用 **Ctrl+F5**（Windows）或 **Cmd+Shift+R**（Mac）

### 如果还是不行

有两个选择：

#### 选项A: 手动创建ACF字段组（临时方案）

1. WordPress后台 → **自定义字段**（或搜索 ACF）
2. 点击 **"新增字段组"**
3. 标题: `Project Details`
4. 手动添加字段（参考README.md中的字段映射表）
5. 在"位置"设置: Post Type is equal to Immigration Project
6. 保存

#### 选项B: 联系技术支持

- 这可能是主机环境特定的问题
- 请收集以下信息:
  - WordPress 版本
  - PHP 版本
  - ACF 版本
  - 其他活跃的插件列表
  - 错误日志（查看 wp-content/debug.log）

## 技术细节

### 为什么这个问题会发生？

ACF 有特定的初始化流程：
1. ACF 插件首先在 `plugins_loaded` 钩子加载
2. ACF 然后在 `acf/init` 钩子初始化自己的功能
3. 只有在 `acf/init` 或之后，`acf_add_local_field_group()` 函数才能使用

之前的代码在 `init` 钩子中注册字段，这时 ACF 可能还没有完全准备好。

### 修复的工作原理

新的代码采用两个钩子：

```php
// 优先尝试使用 ACF 的专用钩子
add_action('acf/init', array($this, 'register_acf_fields'));

// 如果 acf/init 没有触发，使用 plugins_loaded 作为备用
add_action('plugins_loaded', array($this, 'register_acf_fields_fallback'), 20);
```

这确保了无论 ACF 何时加载，我们的字段组都会被正确注册。

## 预防措施

为了避免以后出现类似问题：

1. **定期更新插件** - 确保所有插件都是最新版本
2. **避免冲突插件** - 某些插件可能与 ACF 冲突，监视活跃插件列表
3. **使用可靠的主机** - 某些低端主机的 PHP 配置可能导致问题
4. **启用调试模式** - 编辑 wp-config.php:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

## 联系支持

如果问题仍未解决，请提供：

1. 屏幕截图（显示问题）
2. WordPress 版本
3. ACF 版本
4. PHP 版本
5. 活跃插件列表
6. 错误日志内容（wp-content/debug.log）

---

**最后更新**: 2024年
**适用版本**: Shaw Immigration Projects 1.0.0+

