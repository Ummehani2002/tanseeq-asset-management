# How to Push to GitHub

## If Repository Already Exists on GitHub:

```bash
# 1. Add all changes
git add .

# 2. Commit changes
git commit -m "Update asset management system with consistent styling and features"

# 3. Push to GitHub
git push -u origin main
```

## If Creating New Repository:

1. Go to https://github.com/new
2. Create repository named: `asset-management`
3. Don't initialize with README
4. Then run:

```bash
# If remote doesn't exist, add it:
git remote add origin https://github.com/YOUR_USERNAME/asset-management.git

# Or if remote exists but wrong URL, update it:
git remote set-url origin https://github.com/YOUR_USERNAME/asset-management.git

# Push your code
git push -u origin main
```

## If Authentication Required:

You may need to use a Personal Access Token instead of password:
1. Go to GitHub Settings > Developer settings > Personal access tokens
2. Generate new token with `repo` permissions
3. Use token as password when pushing

