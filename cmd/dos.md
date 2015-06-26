# 什么是DOS

DOS是磁盘操作系统(Disk Operation System)的缩写，是一个更久远的操作系统CP/M的翻版。当年比较流行的DOS有：MS-DOS、PC-DOS、DR-DOS、Free-DOS、PTS-DOS、ROM-DOS、JM-DOS等。

其中MS-DOS最著名，Free-DOS最自由开放。[注1]

如果对DOS感兴趣，可以去研究一下Free-DOS。

不过话说Linux和DOS是什么关系呢，查了一下，挺有意思的，Linux是20世纪90年代开始初出现，90年代末开始流行。那就是Linux流行的时候，DOS都已经流行了几十年了吧。[注2]

DOS下可以运行exe,bat,com程序。

## 常用命令

### 磁盘操作
<pre>
  fdisk /mbr 重建主引导记录(可以洗掉还原精灵)
  format [/q][/u][/autotest]   格式化
    /q 快速格式化
    /u 不可恢复
    /autotest  不提示
    /s 创建MSDOS引导盘
</pre>

### 目录操作

<pre>
  dir [目录或文件名] [/S][/W][/P][/A]    列出目录
    /s  在当前目录下查找文件出现在哪个目录
    /w  只显示文件名
    /p  分页
    /a  显示隐藏文件

  cd [目录名]  进入目录
  cd .       表示当前目录
  cd ..      表示进入上一层目录
  cd [/][\]  表示回到当前盘符的根目录

  mkdir [目录名]    创建目录

  md  建立目录
  cd  改变目录
  rd  删除目录
  dir 查看目录
</pre>

### 文件操作

<pre>
  del [目录或文件名] [/f][/s][/q]   删除目录或文件
    /f  删除只读文件
    /s  删除该目录及其下所有内容
    /q  删除前不提示
  copy [目录或文件名] [目标目录]   把目录或文件复制到目标目录
</pre>

### 其他命令

<pre>
  cls 清屏
  ver 显示当前系统版本号
  date [mm-dd-yy] 设置或显示系统日期
</pre>



## DOS番外篇

DOS适合与个人计算机人机交互。

DOS很小，来管理软硬件很方便。

DOS可以直接对硬件进行操控。无需windows中庞大的动态链接库和驱动程序底层。


## DOS启动盘

### 1.DOS核心文件

  IO.SYS  MS-DOS.SYS   COMMAND.SYS

### 2.DOS增强

<pre>
  HIMEM.SYS
      是MS-DOS的XMS内存管理程序,它可以使我们在DOS下使用到640K常规内存以上的内存。

  SMARTDRV.EXE
      可以将内存的一部分模拟成磁盘缓冲以加快文件的存取，启动之后，可以提高很多DOS下有关磁盘的程序执行速度。
  CONFIG.SYS
      是DOS的一个文本文件命令,它告诉操作系统计算机如何初始化。我们在这里用它加载HIMEM.SYS，以获得640K以上的内存支持。
  AUTOEXEC.BAT
      DOS在启动会自动运行autoexec.bat这条文件，在里面装载每次DOS启动必用的程序，类似于Windows中的"启动"功能。在这里我们用它来自动运行SMARTDRV.EXE。
</pre>

### 3.DOS启动盘制作

  如果有兴趣，上网查吧。
















[1] -- [DOS-百度百科](http://baike.baidu.com/subview/365/7971327.htm)

[2] -- [DOS和Linux近年来的发展比较](http://www.jb51.net/article/963.htm)







