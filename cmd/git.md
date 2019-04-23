# Git command

##### 查看某次提交到当前的差异

git df 1620f36 --name-status

##### 将 master 以zip格式打包到指定文件

git archive --format zip --output /path/to/file.zip master
git archive master > /home/hainuo/fds.zip
git archive v0.1 | gzip > site.tgz

##### 打包差异文件(使用过)

git archive -o update.zip HEAD $(git diff --name-only 1620f36)

##### 比较两个版本之间的差异文件，生成一个差异文件压缩包

git archive develop $( git diff v1.1.8_beta13..v1.1.8_beta14  --name-only)|gzip >aaa.zip

##### 把所有 `code-a` 目录下的相关提交整理为一个新的分支 fe_code_a

git subtree split -P code-a -b fe_code_a

##### 清理 `master` 分支上所有跟 `code-b` 目录有关的痕迹

git filter-branch --index-filter "git rm -rf --cached --ignore-unmatch code-b" --prune-empty master

##### 新建目录并初始化git仓库

mkdir ../new-project
cd ../new-project
git init
git pull ../old-project master # 拉取原项目的master分支到新仓库


