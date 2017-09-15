<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="table table-striped table-hover table-condensed">
<thead>
<tr><th class="role">Role</th><th class="users"># users</th></tr>
</thead>
<tbody>
<xsl:for-each select="roles/role">
	<tr class="click" onClick="javascript:document.location='/{/output/page}/{@id}'">
	<td><xsl:value-of select="." /></td>
	<td class="users"><xsl:value-of select="@users" /></td>
	</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group">
<a href="/{/output/page}/new" class="btn btn-default">New role</a>
<a href="/cms" class="btn btn-default">Back</a>
</div>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="role/@id">
<input type="hidden" name="id" value="{role/@id}" />
</xsl:if>
<label for="name">Name:</label>
<input type="text" id="name" name="name" value="{role}" class="form-control">
<xsl:if test="role/@editable='no'">
	<xsl:attribute name="disabled">disabled</xsl:attribute>
</xsl:if>
</input>

<xsl:for-each select="pages/page">
	<div class="role">
		<input type="checkbox" name="{.}" class="role">
			<xsl:if test="@value!='0'">
				<xsl:attribute name="checked">checked</xsl:attribute>
			</xsl:if>
			<xsl:if test="../../role/@editable='no'">
				<xsl:attribute name="disabled">disabled</xsl:attribute>
			</xsl:if>
		</input>
		<xsl:value-of select="." />
	</div>
</xsl:for-each>
<br clear="both" />

<div class="btn-group">
<xsl:if test="role/@editable='yes'">
<input type="submit" name="submit_button" value="Save role" class="btn btn-default" />
</xsl:if>
<a href="/{/output/page}" class="btn btn-default">Cancel</a>
<xsl:if test="role/@id and role/@editable='yes'">
<input type="submit" name="submit_button" value="Delete role" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</div>
</form>

<xsl:if test="role/@id">
<div class="members">
<h4>Users with this role:</h4>
<table class="table table-striped table-condensed">
<thead>
<tr><th>Name</th><th>E-mail address</th></tr>
</thead>
<tbody>
<xsl:for-each select="members/member">
<tr onClick="javascript:location='/cms/user/{@id}'" class="click">
<td><xsl:value-of select="fullname" /></td>
<td><xsl:value-of select="email" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>
<xsl:if test="not(members/member)">(none)</xsl:if>
</div>
</xsl:if>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/roles.png" class="title_icon" />
<h1>Role administration</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
