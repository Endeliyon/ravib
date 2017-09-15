<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="layout_cms">
<html>

<head>
<meta http-equiv="Content-Language" content="{language}" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="author" content="Hugo Leisink" />
<meta name="publisher" content="Hugo Leisink" />
<meta name="copyright" content="Copyright (C) by Hugo Leisink" />
<meta name="description" content="{description}" />
<meta name="keywords" content="{keywords}" />
<meta name="generator" content="Banshee PHP framework v{/output/banshee/version} (http://www.banshee-php.org/)" />
<link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/apple-touch-icon-180x180.png" />
<link rel="shortcut icon" href="/images/favicon/favicon.ico" />
<title><xsl:if test="title/@page!=''"><xsl:value-of select="title/@page" /> - </xsl:if><xsl:value-of select="title" /></title>
<xsl:for-each select="alternates/alternate">
<link rel="alternate" title="{.}"  type="{@type}" href="{@url}" />
</xsl:for-each>
<xsl:for-each select="styles/style">
<link rel="stylesheet" type="text/css" href="{.}" />
</xsl:for-each>
<xsl:if test="inline_css!=''">
<style type="text/css">
<xsl:value-of select="inline_css" />
</style>
</xsl:if>
<xsl:for-each select="javascripts/javascript">
<script type="text/javascript" src="{.}"></script><xsl:text>
</xsl:text></xsl:for-each>
</head>

<body>
<xsl:if test="javascripts/@onload">
	<xsl:attribute name="onLoad">javascript:<xsl:value-of select="javascripts/@onload" /></xsl:attribute>
</xsl:if>
<div class="container">
	<div class="header">
		<div class="title">Banshee Content Management System</div>
	</div>

	<nav class="navbar navbar-default">
		<div class="container">
			<xsl:if test="count(/output/menu/item)>0">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			</xsl:if>

			<div id="navbar" class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<xsl:for-each select="/output/menu/item">
					<li><a href="{link}"><xsl:value-of select="text" /></a></li>
					</xsl:for-each>
				</ul>
			</div>
		</div>
	</nav>

	<div class="content">
		<xsl:apply-templates select="/output/system_warnings" />
		<xsl:apply-templates select="/output/system_messages" />
		<xsl:apply-templates select="/output/content" />
	</div>

	<div class="footer">
		<xsl:if test="/output/user">
		<span>Logged in as <a href="/profile"><xsl:value-of select="/output/user" /></a></span>
		</xsl:if>
		<span>Built upon the <a href="http://www.banshee-php.org/">Banshee PHP framework</a> v<xsl:value-of select="/output/banshee/version" /></span>
		<span>Copyright (c) by Hugo Leisink</span>
	</div>
</div>
<xsl:apply-templates select="/output/internal_errors" />
</body>

</html>
</xsl:template>

</xsl:stylesheet>
