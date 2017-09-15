<?xml version="1.0" ?>
<xsl:stylesheet	version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="layout_ravib">
<html lang="{language}">

<head>
<meta http-equiv="Content-Language" content="{language}" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="author" content="Hugo Leisink" />
<meta name="copyright" content="Copyright (c) by Hugo Leisink" />
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
<script type="text/javascript" src="{.}"></script>
</xsl:for-each>
</head>

<body>
<xsl:if test="javascripts/@onload">
	<xsl:attribute name="onLoad">javascript:<xsl:value-of select="javascripts/@onload" /></xsl:attribute>
</xsl:if>
<div class="wrapper">

<nav class="navbar">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div class="title"><a href="/"><img src="/images/layout/ravib_logo.png" /></a></div>
		</div>

		<div id="navbar" class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
			<xsl:for-each select="/output/menu/item">
			<li><a href="{link}" class="{class}"><xsl:value-of select="text" /></a></li>
			</xsl:for-each>
			</ul>
		</div>
	</div>
</nav>

<div class="header">
	<div class="container">
	</div>
</div>

<div class="content">
	<div class="container">
		<xsl:apply-templates select="/output/system_warnings" />
		<xsl:apply-templates select="/output/system_messages" />
		<xsl:apply-templates select="/output/content" />
	</div>
</div>

<div class="footer">
	<div class="container">
		<xsl:if test="not(/output/user)">
		<span>Copyright &#169; by Hugo Leisink</span>
		</xsl:if>
		<span>Local copy from <a href="https://www.ravib.nl/">www.ravib.nl</a></span>
		<xsl:if test="/output/user">
		<span>Ingelogd als <a href="/profile"><xsl:value-of select="/output/user" /></a></span>
		<span><a href="/session">Sessiebeheer</a></span>
		</xsl:if>
		<xsl:if test="/output/user/@admin='yes'">
		<span><a href="/cms">CMS</a></span>
		</xsl:if>
	</div>
</div>

<xsl:apply-templates select="/output/internal_errors" />

</div>
</body>

</html>
</xsl:template>

</xsl:stylesheet>
