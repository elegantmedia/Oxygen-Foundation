# Change Log

## Version Compatibility

Use versions as below.

| Laravel Version | This Package Version |       Branch |
|----------------:|---------------------:|-------------:|
|             v12 |                  5.x |          5.x |
|             v10 |                  3.x |       master |  
|              v9 |                  2.x |          2.x |  
|              v8 |                  1.x | version/v1.x | 

## v5.0.0
- Illuminate 12 Support
- Requires PHP ^8.2
- Registered secure Scout "keyword" engine
- Added secure token helpers (HasSecureToken); deprecated legacy CreatesUniqueTokens
- Fixed navbar sorting to use numeric comparison then text
- Fixed authorization status for destroy operations (403 Forbidden)
- Fixed namespace import in FollowsConventions trait
- Fixed validation in controller traits to use Request::validate for Laravel 12 compatibility
- Secured legacy CreatesUniqueTokens logic and added high-resolution timestamp tokens; improved HasSecureToken timestamp precision
- Completed HasUuid contract: trait now provides getUuidColumn() and findByUuid()
- Navigation enhancements:
  - Added `setId()` method to NavItem
  - Added active state tracking with `isActive()`, `setActive()`, `setActiveClass()`, `getActiveClass()`
  - Added nested menu items support with `addChild()`, `getChildren()`, `hasChildren()`, `setParent()`, `getParent()`, `hasVisibleChildren()`
  - Added recursive item retrieval with `NavBar::getItemRecursive()`
  - Added `Navigator::addChildItem()` helper method
  - Updated `Navigator::hideItem()` to work with nested items
  - Updated `toArray()` to include computed values (id, url, active, active_class, children)

## v3.0.0
- Illuminate 10 Support
- Dropped PHP 8.0 Support
 
## v2.0.0
- Illuminate 9 Support
- Dropped PHP 7 Support

## v1.0.0
- Illuminate 8 Support
