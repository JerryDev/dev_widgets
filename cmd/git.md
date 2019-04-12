# Git command

# 查看某次提交到当前的差异

git df 1620f36 --name-status

# 打包差异文件

git archive -o update.zip HEAD $(git diff --name-only 1620f36)



