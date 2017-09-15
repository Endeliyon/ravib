<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<div class="access">
<table class="table table-striped table-condensed">
<thead>
<tr><th class="user">user</th>
<xsl:for-each select="roles/role">
	<th class="access"><xsl:value-of select="." /></th>
</xsl:for-each>
<th></th></tr>
</thead>
<tbody>
<xsl:for-each select="users/user">
	<tr><td><xsl:value-of select="@name" /></td>
	<xsl:for-each select="role">
		<td class="access">
		<xsl:choose>
			<xsl:when test=".=0">
				<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
			</xsl:when>
			<xsl:otherwise>
				<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
			</xsl:otherwise>
		</xsl:choose>
		</td>
	</xsl:for-each>
	<td></td></tr>
</xsl:for-each>
</tbody>
</table>
</div>

<div class="access">
<table class="table table-striped table-condensed">
<thead>
<tr><th class="module">module</th>
<xsl:for-each select="roles/role">
	<th class="access"><xsl:value-of select="." /></th>
</xsl:for-each>
<th></th></tr>
</thead>
<tbody>
<xsl:for-each select="modules/module">
	<tr><td><xsl:value-of select="@url" /></td>
	<xsl:for-each select="access">
		<td class="access">
		<xsl:choose>
			<xsl:when test=".=0">
				<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
			</xsl:when>
			<xsl:otherwise>
				<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
			</xsl:otherwise>
		</xsl:choose>
		</td>
	</xsl:for-each>
	<td></td></tr>
</xsl:for-each>
</tbody>
</table>
</div>

<xsl:if test="pages/page">
	<div class="access">
	<table class="table table-striped table-condensed">
	<thead>
	<tr><th class="module">url</th>
	<xsl:for-each select="roles/role">
		<th class="access"><xsl:value-of select="." /></th>
	</xsl:for-each>
	<th></th></tr>
	</thead>
	<tbody>
	<xsl:for-each select="pages/page">
		<tr><td><xsl:value-of select="@url" /></td>
		<xsl:for-each select="access">
			<td class="access">
			<xsl:choose>
				<xsl:when test=".=0">
					<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
				</xsl:when>
				<xsl:otherwise>
					<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
				</xsl:otherwise>
			</xsl:choose>
			</td>
		</xsl:for-each>
		<td></td></tr>
	</xsl:for-each>
	</tbody>
	</table>
	</div>
</xsl:if>

<div class="btn-group">
<a href="/cms" class="btn btn-default">Back</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/access.png" class="title_icon" />
<h1>Access overview</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
