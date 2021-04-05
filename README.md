# AzuraCast Flysystem V2 Extensions

This library contains extended functionality for version 2 of the [Flysystem](https://flysystem.thephpleague.com) file
abstraction library.

Much of the functionality is intended to restore a user experience similar to Flysystem V1 and to allow for easier
customization of Flysystem for third-party purposes.

Any portion of the extended classes can be used interchangeably with the base Flysystem classes.

## Added Functionality

### Extended Adapters

- **getMetadata Function:** Supported adapters have had a `getMetadata` call added to replace the need for separate
  metadata calls, in keeping with the functionality of Flysystem 1.x. This also reduces redundant API calls if you need
  the full metadata for a file and use the `getMetadata` function at the Filesystem level.

- **Lazy-Loading StorageAttributes:** To ensure all metadata is available in the `StorageAttributes` return object, even
  if some items may be more computationally expensive, some metadata attributes can be supplied as callable functions
  that resolve when called. This is currently only the case for the `visibility` attribute, because it's the most
  computationally intensive one.

- **On-Disk Adapter Optimizations:** The local, on-disk adapter implements a new `LocalAdapterInterface` with a single
  method: `getLocalPath`; if your application has optimizations built in for on-disk filesystems, this allows you to
  immediately identify if the filesystem you're using is on-disk or not.

### Filesystem Changes

- Additional metadata functions are exposed:
    - `getMetadata` to return any available `StorageAttributes` in one object
    - `isFile`, which corresponds to the `isFile()` function in `StorageAttributes`
    - `isDir`, which corresponds to the `isDir()` function in `StorageAttributes`

- A new method, `getAdapter`, is exposed that allows direct access to the underlying adapter interface.

- Helper classes and methods to further distinguish between local and remote filesystems:
    - `LocalFilesystem` intended for use with the `LocalFilesystemAdapter`
    - `RemoteFilesystem` with an optional `$local_path` parameter, intended for remote adapters that still may need a
      temporary local file to be created for interaction with other scripts,
    - `upload`, a helper which copies from the local disk to the destination filesystem,
    - `download`, a helper which copies from the source filesystem to the local disk,
    - `withLocalFile`, a helper which supplies a callable function with a path to a file that is guaranteed to be
      on-disk, even if the filesystem itself is remote.

## Supported Adapters

Currently, this library is a monorepo consisting of all adapters used by the primary application making use of this
library:

- **Local**: A local, on-disk filesystem.
- **AwsS3**: A remote filesystem for cloud storage supporting the AWS S3 storage API.
- **Dropbox**: A remote filesystem for cloud storage on the Dropbox application.
